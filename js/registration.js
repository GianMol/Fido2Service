import { PRE_FIDO2SERVICE_HOSTNAME, FIDO2SERVICE_HOSTNAME, FIDO2SERVICE_PRE_REGISTRATION_PATH, FIDO2SERVICE_REGISTRATION_PATH, challengeToBuffer, responseToBase64, showLoading } from './constants.js';

function callFIDO2RegistrationToken(intent, challenge, data) {
    let challengeBuffer = challengeToBuffer(challenge);
    let credentialsContainer = window.navigator;
    credentialsContainer.credentials.create({ publicKey: challengeBuffer })
    .then(credResp => {
        let credResponse = responseToBase64(credResp);
        credResponse.username = data.username;
        credResponse.firstname = data.firstname;
        credResponse.lastname = data.lastname;
        credResponse.displayname = data.displayname;
        credResponse.intent = intent;
        let url = PRE_FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_REGISTRATION_PATH;
        fetch(url, {
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
                window.location.replace(window.location.protocol + "//" + window.location.host + "/fido2service/Fido2Service/php/login.php?registered=true");
            } else {
                alert(register_json.status + ": " +  register_json.statusText);
            }
            showLoading(false);
        })
        .catch((err) => {
            showLoading(false);
            alert(err);
        })
    })
    .catch(error => {
        showLoading(false);
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
        showLoading(true);
        let url = PRE_FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_PRE_REGISTRATION_PATH;
        fetch(url, {
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
                showLoading(false);
            }
        })
        .catch((err) => {
            showLoading(false);
            alert(err);
        })
    }
}

const button = document.getElementById('submit-button');
button.addEventListener('click', handle_submit);