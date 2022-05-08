import { PRE_FIDO2SERVICE_HOSTNAME, FIDO2SERVICE_HOSTNAME, FIDO2SERVICE_PRE_REGISTRATION_PATH, FIDO2SERVICE_REGISTRATION_PATH, FIDO2SERVICE_LOGIN_PATH, challengeToBuffer, responseToBase64, showLoading } from './constants.js';

// this is the second phase of the task: using the information received by the preregister endpoint, RP client will call the register endpoint of RP server, which will, then, call the register endpoint of FIDO2 server
function callFIDO2RegistrationToken(challenge, data) {
    // "challenge" is the parameter containing all the data the preregister endpoint of RP server has sent: this information has been created by the preregister endpoint of FIDO2 server
    let challengeBuffer = challengeToBuffer(challenge); // the challenge is turned onto a buffer.

    // call to WebAuthn API create needed to communicate with the user authenticator in order to generate a key pair and provide the response for a challenge
    let credentialsContainer = window.navigator;
    credentialsContainer.credentials.create({ publicKey: challengeBuffer })
    .then(credResp => {
        // success case: the response is ready and accessible
        // in this case, the response of the WebAuthn API is used to create data to be sent to register API of RP server; all the needed data are added to the response
        let credResponse = responseToBase64(credResp);
        credResponse.username = data.username;
        credResponse.firstname = data.firstname;
        credResponse.lastname = data.lastname;
        credResponse.displayname = data.displayname;

        let url = PRE_FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_REGISTRATION_PATH; // the register endpoint path is generated
        fetch(url, { // here, the endpoint is called using the fetch function.
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(credResponse)
        })
        .then((register_response) => { // fetch has ended successfully
            return register_response.json(); // conversion of the result of fetch in a json object. This will return another promise
        })
        .then((register_json) => { // json conversion has ended successfully
            if(register_json.status === "200"){ // checking the status of the return object. If the status is 200, then the process has been executed successfully
                window.location.replace(window.location.protocol + "//" + window.location.host +  FIDO2SERVICE_LOGIN_PATH + "?registered=true"); // automatic redirection to the Login page, in which the newly registered user can execute the authentication. The query in the URL "?registered=true" is needed to let the application know this redirection has been executed due to a successfull registration
            } else { // if the status is not 200, then an error has occured and the task cannot end correctly
                alert(register_json.status + ": " +  register_json.statusText); // an alert is popped-up showing the error
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

// this is the first phase of the task: the preregister endpoint of RP server is called, which will, then, call the preregister endpoint of FIDO2 server 
const handle_submit = function(event){
    event.preventDefault(); // so that every other behaviours associated to the pression of the button is executed
    const form = document.getElementById('register-form'); // obtaining the reference of the register form
    if(form.firstname.value.length === 0 || form.lastname.value.length === 0 || form.username.value.length === 0 || form.displayname.value.length === 0){ // checking that the form fields have valid values
        // error case: one or more parameters have not been inserted
        // handling of errors. In this section of code, the error field associated to the case of username not provided is shown, while other error fields are hidden
        let error = document.getElementById('parameters-error'); // obtaining the reference of the parameters-error field, which must be shown

        // the field is shown removing the "hidden" class, if it is not shown yet
        if(error.classList.contains('hidden')){
            error.classList.remove('hidden');
        } 
        error = document.getElementById('username-error'); // obtaining the reference of the username-error field, which must be shown in case parameters have been provided but are not valid, for instance in case of username already used by another user
        
        // in case the field has been shown, then it is here hidden and cleared up
        if(!error.classList.contains('hidden')){
            error.textContent = "";
            error.classList.add('hidden');
        } 
    }
    else{
        // in case the parameters have been provided, generating of data to be sent to the RP server
        let data = {
            firstname: form.firstname.value,
            lastname: form.lastname.value,
            username: form.username.value,
            displayname: form.displayname.value
        }
        showLoading(true); // the loading screen is shown, since the application will call a time-consuming function, fetch
        let url = PRE_FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_HOSTNAME + FIDO2SERVICE_PRE_REGISTRATION_PATH; // generation of the correct endpoint
        fetch(url, { // here, the endpoint is called using the fetch function.
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then((preregister_response) => { // fetch has ended successfully
            return preregister_response.json(); // conversion of the result of fetch in a json object. This will return another promise
        })
        .then((preregister_json) => { // json conversion has ended successfully
            if(preregister_json.status === "200"){ // checking the status of the return object. If the status is 200, then the process has been executed successfully
                // in case of success, all the error fields showing past errors must be hidden and cleaned up
                let error = document.getElementById('parameters-error');
                if(!error.classList.contains('hidden')) error.classList.add('hidden');
                error = document.getElementById('username-error');
                if(!error.classList.contains('hidden')){
                    error.textContent = "";
                    error.classList.add('hidden');
                }

                // this is the call of the second function, representing the second step in RP client side
                // preregister_json.result must be parsed; afterwards, its parameter "Response" will be accessible: it needs to be stringified 
                callFIDO2RegistrationToken(JSON.stringify(JSON.parse(preregister_json.result).Response), data);
            } else { // if the status is not 200, then an error has occured and the task must be interrupted
                if(preregister_json.status === "409"){ // in case the status is 409, then the user has chosen a username already used by another user
                    // other errors must be hidden
                    let error = document.getElementById('parameters-error');
                    if(!error.classList.contains('hidden')) error.classList.add('hidden');

                    // the correct error must be filled and shown 
                    error = document.getElementById('username-error');
                    error.textContent = preregister_json.statusText;
                    error.classList.remove('hidden');
                }
                else{ // in case the status is not 200 nor 409, a simple alert is popped-up showing information about the error
                    alert(preregister_json.status + ": " +  preregister_json.statusText);
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

// adding the event listener on click to the submit button, in order to handle the submission of the parameters
const button = document.getElementById('submit-button');
button.addEventListener('click', handle_submit);
