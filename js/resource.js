import { PRE_FIDO2SERVICE_HOSTNAME, FIDO2SERVICE_HOSTNAME, FIDO2SERVICE_PRE_AUTHORIZATION_PATH, FIDO2SERVICE_AUTHORIZATION_PATH, FIDO2SERVICE_DEREGISTER_PATH, FIDO2SERVICE_LOGIN_PATH, challengeToBuffer, responseToBase64, showLoading } from './constants.js';

function callFIDO2AuthenticationToken(challenge) {
    let challengeBuffer = challengeToBuffer(challenge);
    let credentialsContainer = window.navigator;
    credentialsContainer.credentials.get({ publicKey: challengeBuffer })
    .then(credResp => {
        let credResponse = responseToBase64(credResp);
        let url = PRE_FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_AUTHORIZATION_PATH;
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(credResponse)
        })
        .then((authorization_response) => {
            return authorization_response.json();
        })
        .then((authorization_json) => {
            if(authorization_json.status === "200"){
                alert("Transazione riuscita!");
            }
            else{
                alert(authorization_json.status + ": " + authorization_json.statusText);
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
    showLoading(true);
    let url = PRE_FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_PRE_AUTHORIZATION_PATH;
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then((preauthorization_response) => {
        return preauthorization_response.json();
    })
    .then((preauthorization_json) => {
        if(preauthorization_json.status === "200"){
            callFIDO2AuthenticationToken(JSON.stringify(JSON.parse(preauthorization_json.result).Response));
        } else {
            alert(preauthorization_json.status + ": " +  preauthorization_json.statusText);
            showLoading(false);
        }
    })
    .catch((err) => {
        alert(err);
        showLoading(false);
    })
}

const handle_deregister = function(event){
    showLoading(true);
    let url = PRE_FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_DEREGISTER_PATH;
    fetch(url, {
        method: 'POST'
    })
    .then((deregister_response) => {
        return deregister_response.json();
    })
    .then((deregister_json) => {
        if(deregister_json.status === "200"){
	    window.location.replace(window.location.protocol + "//" + window.location.host + FIDO2SERVICE_LOGIN_PATH);
        } else {
            alert(deregister_json.status + ": " +  deregister_json.statusText);
        }
        showLoading(false);
    })
    .catch((err) => {
        alert(err);
        showLoading(false);
    });
}

const button = document.getElementById('submit-button');
button.addEventListener('click', handle_submit);
const deregister = document.getElementById('deregister');
deregister.addEventListener('click', handle_deregister);
