import { custom_decode, custom_encode, showLoading } from './constants.js';

// this function makes possible to hide modal views
const close_details = function(event){
    // every transaction is identified by the last digits of the id parameter: in this way, it is possible to recognize the correct DOM items owning the information about the specific transaction
	const id = event.currentTarget.id.substring(event.currentTarget.id.lastIndexOf('_') + 1); // here, the crucial information of the identifier is computed

	// getting the reference of the button "verify_signature", if it exists within the modal. It is needed so that the modal doesn't close by clicking this button
	const button = document.getElementById("verify_signature_" + id);
	if(button === event.target){
		return;
	}

    const modal = event.currentTarget;

	// close the modal view and reset the page to the previous settings
    document.body.classList.remove("no-scroll");
    modal.classList.remove("scroll");
    modal.classList.add("hidden");
}

// this function makes possible to show modal views
const handle_details = function(event){
    event.preventDefault(); // so that every other behaviours associated to the pression of the button is executed
    // every transaction is identified by the last digits of the id parameter: in this way, it is possible to recognize the correct DOM items owning the information about the specific transaction
	const id = event.currentTarget.id.substring(event.currentTarget.id.lastIndexOf('_') + 1); // here, the crucial information of the identifier is computed
	const modal = document.getElementById("result_" + id); // the reference of the modal view is here obtained

	document.body.classList.add("no-scroll"); // so that it is impossible to scroll the underlying page
    modal.getElementsByClassName.top = window.pageYOffset + 'px'; // setting the top of the modal equal to the vertical offset of the page, in order to cover the entire screen
    modal.classList.add("scroll"); // so that the user can scroll the modal view
    modal.classList.remove("hidden"); // show the modal view
    modal.addEventListener("click", close_details); // so that the user can close the modal view just by clicking wherever within the screen
}

// this function handles the transaction confirmation step: here, if the algorithm is recognized as supported, the signature of the transaction is verified 
const handle_submit = function(event){
    event.preventDefault(); // so that every other behaviours associated to the pression of the button is executed
    showLoading(true); // the loading screen is shown, since the application will perform time-consuming functions, such as crypto.subtle.digest, crypto.subtle.importKey or crypto.subtle.verify

	// every transaction is identified by the last digits of the id parameter: in this way, it is possible to recognize the correct DOM items owning the information about the specific transaction
	const id = event.currentTarget.id.substring(event.currentTarget.id.lastIndexOf('_') + 1); // here, the crucial information of the identifier is computed

	// data regarding the transaction are here gathered
	const signature = document.getElementById("signature_" + id).innerText;
    let signerPublicKey = document.getElementById("signerPublicKey_" + id).innerText;
    const signingKeyAlgorithm = document.getElementById("signingKeyAlgorithm_" + id).innerText;
    const hash_alg = signingKeyAlgorithm.substring(0, signingKeyAlgorithm.indexOf('w'));
    const formatted_hash_alg = hash_alg.slice(0, 3) + "-" + hash_alg.slice(3);
    const signingKeyType = document.getElementById("signingKeyType_" + id).innerText;
    const authenticatorData = document.getElementById("authenticatorData_" + id).innerText;
    const clientDataJson = document.getElementById("clientDataJson_" + id).innerText;

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

		// declaration of variables needed to import the key and verify the signature
		let alg;
		let format;
		let keyData;

		// filtering based on the algorithm supported. Until now, RSA is the only supported algorithm for the transaction confirmation step
		if(signingKeyType === "RSA"){ // RSA algorithm case
			// assignment of correct values to variables needed to import the key
			alg = {
				name: "RSASSA-PKCS1-V1_5",
				hash: formatted_hash_alg,
			}
			format = "spki";
			keyData = signerPublicKey_buf;
		}
		else{ // in case the algorithm is not supported
			// the error is shown and the loading screen hidden
			console.log("error: key type not supported.");
			alert("error: key type not supported.");
			showLoading(false);
			return;
		}

		window.crypto.subtle.importKey( // the key is here imported using the variables filled before
			format, // format of the key
			keyData, // public key
			alg, // algorithm
			false,
			["verify"] // purpose
		)
		.then((key) => { // the key is successfully imported
			// declaration of variables needed to verify the signature
			let name;

			// filter on the basis of the algorithm used
			if(signingKeyType === "RSA") name = "RSASSA-PKCS1-V1_5";
			else return;

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
				const img = document.getElementById("img_" + id);
				img.src = result ? "../images/ok.png" : "../images/ko.png";
				img.classList.remove("hidden");

				const img_preview = document.getElementById("img_preview_" + id);
				img_preview.src = result ? "../images/ok.png" : "../images/ko.png";
				img_preview.classList.remove("hidden");
				
				const res = document.getElementById("actual_result_" + id); // getting the reference of the hidden part of the modal view of the transaction
				if(res){ // checking whether the reference has been found
					// in positive case, the field must be filled with information about the result and the clientDataJson

					// decoding clientDataJson
					let utf8Decoder = new TextDecoder('utf-8');
					let client_json = JSON.parse(utf8Decoder.decode(custom_decode(clientDataJson)));
					res.textContent = "";

					// filling the section of the modal view
					let span = document.createElement("span");
					span.textContent = "data:";
					res.appendChild(span);
					let div = document.createElement("div");
					div.textContent = custom_encode(data);
					res.appendChild(div);

					span = document.createElement("span");
					span.textContent = "challenge:";
					res.appendChild(span);
					div = document.createElement("div");
					div.textContent = client_json.challenge;
					res.appendChild(div);

					span = document.createElement("span");
					span.textContent = "crossOrigin:";
					res.appendChild(span);
					div = document.createElement("div");
					div.textContent = client_json.crossOrigin ? "true" : "false";
					res.appendChild(div);

					span = document.createElement("span");
					span.textContent = "origin:";
					res.appendChild(span);
					div = document.createElement("div");
					div.textContent = client_json.origin;
					res.appendChild(div);
					
					span = document.createElement("span");
					span.textContent = "type:";
					res.appendChild(span);
					div = document.createElement("div");
					div.textContent = client_json.type;
					res.appendChild(div);

					span = document.createElement("span");
					span.textContent = "result: " + (result ? "valid" : "not valid");
					res.appendChild(span);

					// showing this section
					res.classList.remove("hidden");
				}
				showLoading(false); // turning off the loading screen
			})
			.catch(err => {console.log(err); showLoading(false);}); // error in verifying the signature
		})
		.catch(err => {console.log(err); showLoading(false);}); // error in importing the key
    })
	.catch(err => {console.log(err); showLoading(false);}); // error in creating the digest of clientDataJson
}

// adding the event listener on click to all verify signature buttons, in order to handle the confirmation of transactions step
const verifiers = document.getElementsByClassName('verify_signature');
let i;
for(i= 0; i < verifiers.length; i++){
    verifiers[i].addEventListener('click', handle_submit);
}

// adding the event listener on click to all view details buttons, in order to handle the view of the modal screen
const information = document.getElementsByClassName('view_details');
for(i= 0; i < information.length; i++){
    information[i].addEventListener('click', handle_details);
}