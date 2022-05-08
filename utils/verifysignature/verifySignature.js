import { custom_decode, showLoading } from '../../js/constants.js';

// this function handles the transaction confirmation step: here, if the algorithm is recognized as supported, the signature of the transaction is verified 
const handle_submit = function(event){
    event.preventDefault(); // so that every other behaviours associated to the pression of the button is executed
    showLoading(true); // the loading screen is shown, since the application will perform time-consuming functions, such as crypto.subtle.digest, crypto.subtle.importKey or crypto.subtle.verify

	// data regarding the transaction are here gathered
	const signature = document.getElementById("signature").value;
    let signerPublicKey = document.getElementById("signerPublicKey").value;
    const signingKeyAlgorithm = document.getElementById("signingKeyAlgorithm").value;
    const hash_alg = signingKeyAlgorithm.substring(0, signingKeyAlgorithm.indexOf('w'));
    const formatted_hash_alg = hash_alg.slice(0, 3) + "-" + hash_alg.slice(3);
    const authenticatorData = document.getElementById("authenticatorData").value;
    const clientDataJson = document.getElementById("clientDataJson").value;

	if(signingKeyAlgorithm !== "SHA256withRSA"){ // in case the algorithm is not supported
		// the error is shown and the loading screen hidden
		const msg = "Error: key type not supported"
		console.log(msg);
		const res = document.getElementById("result");
		res.textContent = msg;
		showLoading(false);
		return;
	}

	// here, it is computed the data used in authorization phase to compute the signature. This data is computed as: authenticatorData || hash(clientDataJson)
	// then, first the digest of clientDataJson is computed
    crypto.subtle.digest(formatted_hash_alg, custom_decode(clientDataJson))
    .then(hash => { // in case of success
		// data is computed
		const int_authenticatorData = new Int8Array(custom_decode(authenticatorData));
		const int_hash = new Int8Array(hash);
		const int_data = new Int8Array(int_authenticatorData.byteLength + int_hash.byteLength);
		int_data.set(int_authenticatorData, 0);
		int_data.set(int_hash, int_authenticatorData.byteLength);
		let data = int_data.buffer;

		// signature and public key are converted in buffers
		const int_signature = new Int8Array(custom_decode(signature));
		const signature_buf = int_signature.buffer;
		let int_signerPublicKey = new Int8Array(custom_decode(signerPublicKey));
		let signerPublicKey_buf = int_signerPublicKey.buffer;

		// initialization of variables needed to import the key and verify the signature
		let alg = {
			name: "RSASSA-PKCS1-V1_5",
			hash: formatted_hash_alg,
		}
		let format = "spki";
		let keyData = signerPublicKey_buf;

		window.crypto.subtle.importKey( // the key is here imported using the variables filled before
			format, // format of the key
			keyData, // public key
			alg, // algorithm
			false,
			["verify"] // purpose
		)
		.then((key) => { // the key is successfully imported
			// declaration of variables needed to verify the signature
			let name = "RSASSA-PKCS1-V1_5";

			window.crypto.subtle.verify( // verification of the signature using the parameters filled before
				{
					name: name,
					hash: {
						name: formatted_hash_alg
					}
				}, // algorithm
				key, // public key
				signature_buf, // signature
				data // data signature is based on
			)
			.then((result) => { // the verification step ended
				// putting the right image icon, based on the result, in the preview of the transaction and in the modal view
				const img = document.getElementById("result-img");
				img.src = result ? "../../images/ok.png" : "../../images/ko.png";
				
				const res = document.getElementById("result");
				res.textContent = result ? "Verification OK" : "Verification Failed";

				const resarea = document.getElementById("resultarea");
				resarea.classList.remove("hidden");

				showLoading(false); // turning off the loading screen
			})
			.catch(err => {console.log(err); showLoading(false);}); // error in verifying the signature
		})
		.catch(err => {console.log(err); showLoading(false);}); // error in importing the key
    })
	.catch(err => {console.log(err); showLoading(false);}); // error in creating the digest of clientDataJson
}


const button = document.getElementById('submit-button');
button.addEventListener('click', handle_submit);