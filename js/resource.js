import { PRE_FIDO2SERVICE_HOSTNAME, FIDO2SERVICE_HOSTNAME, FIDO2SERVICE_PRE_AUTHORIZATION_PATH, FIDO2SERVICE_AUTHORIZATION_PATH, FIDO2SERVICE_DEREGISTER_PATH, FIDO2SERVICE_LOGIN_PATH, challengeToBuffer, responseToBase64, showLoading } from './constants.js';

// this is the second phase of the task: using the information received by the preauthorize endpoint, RP client will call the authorize endpoint of RP server, which will, then, call the authorize endpoint of FIDO2 server
function callFIDO2AuthorizationToken(challenge) {
    // "challenge" is the parameter containing all the data the preauthorize endpoint of RP server has sent: this information has been created by the preauthorize endpoint of FIDO2 server
    let challengeBuffer = challengeToBuffer(challenge); // the challenge is turned onto a buffer.

    // call to WebAuthn API get needed to communicate with the user authenticator in order to execute cryptographic functions needed to perform the response of the challenge
    let credentialsContainer = window.navigator;
    credentialsContainer.credentials.get({ publicKey: challengeBuffer })
    .then(credResp => {
        // success case: the response is ready and accessible
        let credResponse = responseToBase64(credResp); // in this case, the response of the WebAuthn API is used to create data to be sent to authorize API of RP server
        let url = PRE_FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_AUTHORIZATION_PATH; // the authenticate endpoint path is generated
        fetch(url, { // here, the endpoint is called using the fetch function.
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(credResponse)
        })
        .then((authorization_response) => { // fetch has ended successfully
            return authorization_response.json(); // conversion of the result of fetch in a json object. This will return another promise
        })
        .then((authorization_json) => { // json conversion has ended successfully
            if(authorization_json.status === "200"){ // checking the status of the return object. If the status is 200, then the process has been executed successfully
                alert("Transaction done!");
            }
            else{ // if the status is not 200, then an error has occured and the task cannot end correctly
                alert(authorization_json.status + ": " + authorization_json.statusText); // an alert is popped-up showing the error
            }
            // independently of the success of the task, here the task ends and the loading screen must be turned off
            showLoading(false);
        })
        .catch((err) => { // in case of error in promises, the loading screen must be turned off and an alert must be popped-up showing the error
            showLoading(false);
            alert(err);
        })
    })
    .catch(error => { // in case of error in the WebAuthn API call, the loading screen must be turned off and an alert must be popped-up showing the error
        showLoading(false);
        alert(error);
    });
}


// this is the first phase of the task: the preauthorize endpoint of RP server is called, which will, then, call the preauthorize endpoint of FIDO2 server 
const handle_submit = function(event){
    event.preventDefault(); // so that every other behaviours associated to the pression of the button is executed
    // generating of data to be sent to the RP server
    let date = new Date();
    const pcInfo = document.getElementById('pc-information').innerText;
    const pcPrice = document.getElementById('pc-price').innerText;
    let data = {
	txpayload: pcInfo + " : " + pcPrice + " - Date : " +
		date.getDate() + "/" +
		(date.getMonth() + 1) + "/" +
		date.getFullYear() + " " +
		date.getHours() + ":" +
		date.getMinutes() + ":" +
		date.getSeconds()
    };
    showLoading(true); // the loading screen is shown, since the application will call a time-consuming function, fetch
    const url = PRE_FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_PRE_AUTHORIZATION_PATH; // generation of the correct endpoint
    fetch(url, { // here, the endpoint is called using the fetch function
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
	body: JSON.stringify(data)
    })
    .then((preauthorization_response) => { // fetch has ended successfully
        return preauthorization_response.json(); // conversion of the result of fetch in a json object. This will return another promise
    })
    .then((preauthorization_json) => { // json conversion has ended successfully
        if(preauthorization_json.status === "200"){ // checking the status of the return object. If the status is 200, then the process has been executed successfully
            // this is the call of the second function, representing the second step in RP client side
            // preauthorization_json.result must be parsed; afterwards, its parameter "Response" will be accessible: it needs to be stringified 
            callFIDO2AuthorizationToken(JSON.stringify(JSON.parse(preauthorization_json.result).Response));
        } else { // if the status is not 200, then an error has occured and the task must be interrupted
            // a simple alert is popped-up showing information about the error and the loading screen is turned off
            alert(preauthorization_json.status + ": " +  preauthorization_json.statusText);
            showLoading(false);
        }
    })
    .catch((err) => { // in case of error in promises, the loading screen must be turned off and an alert must be popped-up showing the error
        alert(err);
        showLoading(false);
    })
}

// this function is used to perform the deregistration task: in this task, from the point of view of RP client, the step is only one and there is no need of using WebAuthn APIs since the communication with the authenticator is not needed
const handle_deregister = function(){
    showLoading(true); // the loading screen is shown, since the application will call a time-consuming function, fetch
    let url = PRE_FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_DEREGISTER_PATH; // generation of the correct endpoint
    fetch(url, { // here, the endpoint is called using the fetch function
        method: 'POST'
    })
    .then((deregister_response) => { // fetch has ended successfully
        return deregister_response.json(); // conversion of the result of fetch in a json object. This will return another promise
    })
    .then((deregister_json) => { // json conversion has ended successfully
        if(deregister_json.status === "200"){ // checking the status of the return object. If the status is 200, then the process has been executed successfully
	    window.location.replace(window.location.protocol + "//" + window.location.host + FIDO2SERVICE_LOGIN_PATH); // automatic redirection to the Login page
        } else { // if the status is not 200, then an error has occured and the task cannot end correctly
            alert(deregister_json.status + ": " +  deregister_json.statusText); // a simple alert is popped-up showing information about the error
        }
        // independently of the success of the task, here the task ends and the loading screen must be turned off
        showLoading(false);
    })
    .catch((err) => { // in case of error in promises, the loading screen must be turned off and an alert must be popped-up showing the error
        alert(err);
        showLoading(false);
    });
}

// adding the event listener on click to the submit button, in order to handle the start of a new transaction
const button = document.getElementById('submit-button');
button.addEventListener('click', handle_submit);

// adding the event listener on click to the deregister button, in order to handle the deregister task
const deregister = document.getElementById('deregister');
deregister.addEventListener('click', handle_deregister);
