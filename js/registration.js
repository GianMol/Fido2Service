let chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';
let lookup = new Uint8Array(256);
for (let i = 0; i < chars.length; i++) {
  lookup[chars.charCodeAt(i)] = i;
}

const encode = function (arraybuffer) {
    let bytes = new Uint8Array(arraybuffer),
        i, len = bytes.length, base64url = '';
    for (i = 0; i < len; i += 3) {
        base64url += chars[bytes[i] >> 2];
        base64url += chars[((bytes[i] & 3) << 4) | (bytes[i + 1] >> 4)];
        base64url += chars[((bytes[i + 1] & 15) << 2) | (bytes[i + 2] >> 6)];
        base64url += chars[bytes[i + 2] & 63];
    }
    
    if ((len % 3) === 2) {
        base64url = base64url.substring(0, base64url.length - 1);
    } else if (len % 3 === 1) {
        base64url = base64url.substring(0, base64url.length - 2);
    }
    return base64url;
};
  
const decode = function (base64string) {
    let bufferLength = base64string.length * 0.75,
    len = base64string.length, i, p = 0,
    encoded1, encoded2, encoded3, encoded4;
    let bytes = new Uint8Array(bufferLength);
    for (i = 0; i < len; i += 4) {
        encoded1 = lookup[base64string.charCodeAt(i)];
        encoded2 = lookup[base64string.charCodeAt(i + 1)];
        encoded3 = lookup[base64string.charCodeAt(i + 2)];
        encoded4 = lookup[base64string.charCodeAt(i + 3)];
        bytes[p++] = (encoded1 << 2) | (encoded2 >> 4);
        bytes[p++] = ((encoded2 & 15) << 4) | (encoded3 >> 2);
        bytes[p++] = ((encoded3 & 3) << 6) | (encoded4 & 63);
    }
    return bytes.buffer
};

const challengeToBuffer = function(input) {
    input = JSON.parse(input);
    input.challenge = decode(input.challenge);
    if(typeof input.user !== 'undefined') {
        input.user.id = decode(input.user.id);
    }
  
    if (input.excludeCredentials) {
        for (let i = 0; i < input.excludeCredentials.length; i++) {
            input.excludeCredentials[i].id = input.excludeCredentials[i].id.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
            input.excludeCredentials[i].id = decode(input.excludeCredentials[i].id);
        }
    }
    
    if (input.allowCredentials) {
      for (let i = 0; i < input.allowCredentials.length; i++) {
        input.allowCredentials[i].id = input.allowCredentials[i].id.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
        input.allowCredentials[i].id = decode(input.allowCredentials[i].id);
      }
    }
    return input;
}

const responseToBase64 = function(input) {
    let copyOfDataResponse = {};
    copyOfDataResponse.id = input.id;
    copyOfDataResponse.rawId = encode(input.rawId);
    if(typeof input.response.attestationObject !== 'undefined') {
        copyOfDataResponse.attestationObject = encode(input.response.attestationObject);
    }
    if(typeof input.response.authenticatorData !== 'undefined') {
        copyOfDataResponse.authenticatorData = encode(input.response.authenticatorData);
        copyOfDataResponse.signature = encode(input.response.signature);
        copyOfDataResponse.userHandle = encode(input.response.userHandle);
    }
    copyOfDataResponse.clientDataJSON = encode(input.response.clientDataJSON);
    copyOfDataResponse.type = input.type;
    return copyOfDataResponse;
}

function callFIDO2RegistrationToken(intent, challenge, data) {
    let challengeBuffer = challengeToBuffer(challenge);
    let credentialsContainer = window.navigator;
    credentialsContainer.credentials.create({ publicKey: challengeBuffer })
    .then(credResp => {
        let credResponse = responseToBase64(credResp);
        credResponse.username = data.username;
        credResponse.firstname = data.firstname;
        credResponse.lastname = data.lastname;
        credResponse.intent = intent;
        fetch("https://fido2service.strongkey.com/fido2service/Fido2Service/php/api/register.php", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(credResponse)
        })
        .then((register_response) => {
            return register_response.json();
        })
        .then((register_json) => {
            if(register_json.status === "200"){
                //window.location.replace(window.location.protocol + "//" + window.location.host + "/login");
                alert("Registration complete");
            } else {
                alert(register_json.status + ": " +  register_json.statusText);
            }
        })
        .catch((err) => {
            console.log(err);
            alert(err);
        })
    })
    .catch(error => {
        alert(error);
    });
}


const handle_submit = function(event){
    event.preventDefault();
    const form = document.getElementById('register-form');
    if(form.firstname.value.length === 0 || form.lastname.value.length === 0 || form.username.value.length === 0 || form.displayname.value.length === 0){
        let error = document.getElementById('parameters-error');
        error.classList.remove('hidden');
        error = document.getElementById('username-error');
        if(!error.classList.contains('hidden')){
            error.textContent = "";
            error.classList.add('hidden');
        } 
    }
    else{
        let data = {
            firstname: form.firstname.value,
            lastname: form.lastname.value,
            username: form.username.value,
            displayname: form.displayname.value
        }
        fetch("https://fido2service.strongkey.com/fido2service/Fido2Service/php/api/preregister.php", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then((preregister_response) => {
            return preregister_response.json();
        })
        .then((preregister_json) => {
            if(preregister_json.status === "200"){
                let error = document.getElementById('parameters-error');
                if(!error.classList.contains('hidden')) error.classList.add('hidden');
                error = document.getElementById('username-error');
                if(!error.classList.contains('hidden')){
                    error.textContent = "";
                    error.classList.add('hidden');
                } 


                callFIDO2RegistrationToken("registration", JSON.stringify(JSON.parse(preregister_json.result).Response), data);
            } else {
                if(preregister_json.status === "409"){
                    let error = document.getElementById('parameters-error');
                    if(!error.classList.contains('hidden')) error.classList.add('hidden');

                    error = document.getElementById('username-error');
                    error.textContent = preregister_json.statusText;
                    error.classList.remove('hidden');
                }
                else{
                    alert(preregister_json.status + ": " +  preregister_json.statusText);
                }
            }
        })
        .catch((err) => {
            alert(err);
        })
    }
}

const button = document.getElementById('submit-button');
button.addEventListener('click', handle_submit);