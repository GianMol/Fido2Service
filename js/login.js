import { PRE_FIDO2SERVICE_HOSTNAME, FIDO2SERVICE_HOSTNAME, FIDO2SERVICE_PRE_AUTHENTICATION_PATH, FIDO2SERVICE_AUTHENTICATION_PATH, FIDO2SERVICE_RESOURCE_PATH, challengeToBuffer, responseToBase64, showLoading } from './constants.js';

// this is the second phase of the task: using the information received by the preauthenticate endpoint, RP client will call the authenticate endpoint of RP server, which will, then, call the authenticate endpoint of FIDO2 server
function callFIDO2AuthenticationToken(challenge, data) {
    // "challenge" is the parameter containing all the data the preauthenticate endpoint of RP server has sent: this information has been created by the preauthenticate endpoint of FIDO2 server
    let challengeBuffer = challengeToBuffer(challenge); // the challenge is turned onto a buffer.

    // call to WebAuthn API get needed to communicate with the user authenticator in order to execute cryptographic functions needed to perform the response of the challenge
    let credentialsContainer = window.navigator;
    credentialsContainer.credentials.get({ publicKey: challengeBuffer })
    .then(credResp => {
        // success case: the response is ready and accessible
        // in this case, the response of the WebAuthn API is used to create data to be sent to authenticate API of RP server; all the needed data are added to the response
        let credResponse = responseToBase64(credResp);
        credResponse.username = data.username;

        let url = PRE_FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_AUTHENTICATION_PATH; // the authenticate endpoint path is generated
        fetch(url, { // here, the endpoint is called using the fetch function.
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(credResponse)
        })
        .then((authenticate_response) => { // fetch has ended successfully
            return authenticate_response.json(); // conversion of the result of fetch in a json object. This will return another promise
        })
        .then((authenticate_json) => { // json conversion has ended successfully
            if(authenticate_json.status === "200"){ // checking the status of the return object. If the status is 200, then the process has been executed successfully
                window.location.replace(window.location.protocol + "//" + window.location.host + FIDO2SERVICE_RESOURCE_PATH); // automatic redirection to the Resource page
            }
            else{ // if the status is not 200, then an error has occured and the task cannot end correctly
                // in this case, the username-error field must be filled and shown 
                error = document.getElementById('username-error');
                error.textContent = authenticate_json.statusText;
                error.classList.remove('hidden');
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

// this is the first phase of the task: the preauthenticate endpoint of RP server is called, which will, then, call the preauthenticate endpoint of FIDO2 server 
const handle_submit = function(event){
    event.preventDefault(); // so that every other behaviours associated to the pression of the button is executed
    const form = document.getElementById('login-form'); // obtaining the reference of the login form
    if(form.username.value.length === 0){ // checking that the username field, the only one present in the form, has a valid value
        // error case: the username has not been inserted
        // handling of errors. In this section of code, the error field associated to the case of username not provided is shown, while other error fields are hidden
        let error = document.getElementById('parameters-error'); // obtaining the reference of the parameters-error field, which must be shown

        // the field is shown removing the "hidden" class, if it is not shown yet
        if(error.classList.contains('hidden')){
            error.classList.remove('hidden');
        } 
        error = document.getElementById('username-error'); // obtaining the reference of the username-error field, which must be shown in case the username has been provided but it is not valid, for instance in case it doesn't match to any username in database

        // in case the field has been shown, then it is here hidden and cleared up
        if(!error.classList.contains('hidden')){
            error.textContent = "";
            error.classList.add('hidden');
        } 
    }
    else{
        // in case the username has been provided, generating of data to be sent to the RP server
        let data = {
            username: form.username.value,
        }
        showLoading(true); // the loading screen is shown, since the application will call a time-consuming function, fetch
        let url = PRE_FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_PRE_AUTHENTICATION_PATH; // generation of the correct endpoint
        fetch(url, { // here, the endpoint is called using the fetch function
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then((preauthenticate_response) => { // fetch has ended successfully
            return preauthenticate_response.json(); // conversion of the result of fetch in a json object. This will return another promise
        })
        .then((preauthenticate_json) => { // json conversion has ended successfully
            if(preauthenticate_json.status === "200"){ // checking the status of the return object. If the status is 200, then the process has been executed successfully
                // in case of success, all the error fields showing past errors must be hidden and cleaned up
                let error = document.getElementById('parameters-error');
                if(!error.classList.contains('hidden')) error.classList.add('hidden');
                error = document.getElementById('username-error');
                if(!error.classList.contains('hidden')){
                    error.textContent = "";
                    error.classList.add('hidden');
                }

                // this is the call of the second function, representing the second step in RP client side
                // preauthenticate_json.result must be parsed; afterwards, its parameter "Response" will be accessible: it needs to be stringified 
                callFIDO2AuthenticationToken(JSON.stringify(JSON.parse(preauthenticate_json.result).Response), data);
            } else { // if the status is not 200, then an error has occured and the task must be interrupted
                if(preauthenticate_json.status === "409"){ // in case the status is 409, then the user has provided a wrong username or a username which doesn't match to any user in database
                    // other errors must be hidden
                    let error = document.getElementById('parameters-error');
                    if(!error.classList.contains('hidden')) error.classList.add('hidden');

                    // the correct error must be filled and shown 
                    error = document.getElementById('username-error');
                    error.textContent = preauthenticate_json.statusText;
                    if(error.classList.contains('hidden')) error.classList.remove('hidden');
                }
                else{ // in case the status is not 200 nor 409, a simple alert is popped-up showing information about the error
                    alert(preauthenticate_json.status + ": " +  preauthenticate_json.statusText);
                }
                // finally, in case the task must be interrupted, independently of the type of error occured, the loading screen must be hidden
                showLoading(false);
            }
        })
        .catch((err) => { // in case of error in promises, the loading screen must be turned off and an alert must be popped-up showing the error
            showLoading(false);
            alert(err);
        })
    }
}

// adding the event listener on click to the submit button, in order to handle the submission of the username
const button = document.getElementById('submit-button');
button.addEventListener('click', handle_submit);
