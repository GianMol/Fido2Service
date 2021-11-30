import { PRE_FIDO2SERVICE_HOSTNAME, FIDO2SERVICE_HOSTNAME, FIDO2SERVICE_PRE_AUTHENTICATION_PATH, FIDO2SERVICE_AUTHENTICATION_PATH, challengeToBuffer, responseToBase64 } from './constants.js';

function callFIDO2AuthenticationToken(intent, challenge, data) {
    let challengeBuffer = challengeToBuffer(challenge);
    let credentialsContainer = window.navigator;
    credentialsContainer.credentials.get({ publicKey: challengeBuffer })
    .then(credResp => {
        let credResponse = responseToBase64(credResp);
        credResponse.intent = intent;
        credResponse.username = data.username;
        console.log(credResponse);
        let url = PRE_FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_AUTHENTICATION_PATH;
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(credResponse)
        })
        .then((authenticate_response) => {
            return authenticate_response.json();
        })
        .then((authenticate_json) => {
            console.log(authenticate_json);
            if(authenticate_json.status === "200"){
                window.location.replace(window.location.protocol + "//" + window.location.host + "/fido2service/Fido2Service/");
            }
            else{
                error = document.getElementById('username-error');
                error.textContent = authenticate_json.statusText;
                error.classList.remove('hidden');
            }
        })
        .catch((err) => {
            alert(err);
        })
    })
    .catch(error => {
        alert(error);
    });
}


const handle_submit = function(event){
    event.preventDefault();
    const form = document.getElementById('login-form');
    if(form.username.value.length === 0){
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
            username: form.username.value,
        }
        let url = PRE_FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_PRE_AUTHENTICATION_PATH;
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then((preauthenticate_response) => {
            return preauthenticate_response.json();
        })
        .then((preauthenticate_json) => {
            if(preauthenticate_json.status === "200"){
                let error = document.getElementById('parameters-error');
                if(!error.classList.contains('hidden')) error.classList.add('hidden');
                error = document.getElementById('username-error');
                if(!error.classList.contains('hidden')){
                    error.textContent = "";
                    error.classList.add('hidden');
                } 
                callFIDO2AuthenticationToken("authentication", JSON.stringify(JSON.parse(preauthenticate_json.result).Response), data);
            } else {
                if(preauthenticate_json.status === "409"){
                    let error = document.getElementById('parameters-error');
                    if(!error.classList.contains('hidden')) error.classList.add('hidden');

                    error = document.getElementById('username-error');
                    error.textContent = preauthenticate_json.statusText;
                    error.classList.remove('hidden');
                }
                else{
                    alert(preauthenticate_json.status + ": " +  preauthenticate_json.statusText);
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