/* CONSTANTS */

// location of the web application server
export const FIDO2SERVICE_HOSTNAME = "fido2service.strongkey.com";
export const PRE_FIDO2SERVICE_HOSTNAME = "https://";

// location of endpoints of the web application server
export const FIDO2SERVICE_PRE_REGISTRATION_PATH = "/php/api/preregister.php";
export const FIDO2SERVICE_REGISTRATION_PATH = "/php/api/register.php";
export const FIDO2SERVICE_PRE_AUTHENTICATION_PATH = "/php/api/preauthenticate.php";
export const FIDO2SERVICE_AUTHENTICATION_PATH = "/php/api/authenticate.php";
export const FIDO2SERVICE_PRE_AUTHORIZATION_PATH = "/php/api/preauthorize.php";
export const FIDO2SERVICE_AUTHORIZATION_PATH = "/php/api/authorize.php";
export const FIDO2SERVICE_DEREGISTER_PATH = "/php/api/deregister.php";

// location of interfaces of the web application which are needed to automatically redirect the user
export const FIDO2SERVICE_LOGIN_PATH = "/php/login.php";
export const FIDO2SERVICE_RESOURCE_PATH = "/php/resource.php";


// Constant values and utils functions
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
export const custom_encode = function(arraybuffer){
    return encode(arraybuffer);
}
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
export const custom_decode = function(base64string){
    return decode(base64string);
}
export const challengeToBuffer = function(input) {
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
export const responseToBase64 = function(input) {
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


// variables needed for the loading screen, to move the dots from 1 to 3
let loading_counter;
let interval;

// showLoading handles the view or the hide of the loading screen. It wants as input a boolean: if true, the loading must be shown; if false, the loading must be hidden
export const showLoading = function(show){
    const dotsMove = function(){ // this function manages the motion of dots
        let dots = document.getElementById('dots');
        switch(loading_counter){ // the motion depends on the value of a counter
            case 0:
                dots.textContent = ".";
                break;
            case 1:
                dots.textContent = "..";
                break;
            default:
                dots.textContent = "...";
                break;
        }
    
        if(loading_counter == 2) loading_counter = 0; // if the counter arrives to its maximum, then it is reset
        else loading_counter++; // at the end of the function, the counter is incremented or reset
    }

    const dotsClear = function(){ // this function is needed once the loading screen is hidden, so that to clean the changes
        let dots = document.getElementById('dots');
        dots.textContent = '';
    }

    if(show){ // in case the loading screen must be shown
        loading_counter = 0; // the loading counter is reset, so that the loading screen will always start with only one dot
        const loading = document.getElementById('loading'); // taking the reference of the loading screen
        loading.classList.remove('hidden'); // showing the loading screen
        interval = setInterval(dotsMove, 1000); // setting the interval to 1000 milliseconds; every time this time passes, the dots will move

        const navbar = document.getElementById('nav-bar'); // taking the reference of the nav bar
        navbar.classList.add('hidden'); // hiding the nav bar
        navbar.classList.remove('nav-bar'); // removing the nav-bar class

        const bodylayout = document.getElementById('body-layout'); // taking the reference of the body-layout, the rest of the page
        bodylayout.classList.add('hidden'); // hiding the body-layout
        bodylayout.classList.remove('body-layout'); // removing the body-layout class

        // now, the only viewable screen is the loading
    }
    else{ // in case the loading screen must be hidden
        clearInterval(interval); // the interval must stop working
        dotsClear(); // the dots changes must be reset
        interval = null; // the interval variable does not have to point to the previously interval
        loading_counter = 0; // the loading counter is reset

        // loading screen must be hidden
        const loading = document.getElementById('loading');
        loading.classList.add('hidden');

        // nav bar must be shown
        const navbar = document.getElementById('nav-bar');
        navbar.classList.remove('hidden');
        navbar.classList.add('nav-bar');

        // body layout must be shown
        const bodylayout = document.getElementById('body-layout');
        bodylayout.classList.remove('hidden');
        bodylayout.classList.add('body-layout');

        // now, everything turns back how it was before
    }
}