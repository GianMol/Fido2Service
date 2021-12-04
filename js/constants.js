/* CONSTANTS */

export const FIDO2SERVICE_HOSTNAME = "fido2service.strongkey.com";
export const PRE_FIDO2SERVICE_HOSTNAME = "https://";

export const FIDO2SERVICE_PRE_REGISTRATION_PATH = "/php/api/preregister.php";
export const FIDO2SERVICE_REGISTRATION_PATH = "/php/api/register.php";
export const FIDO2SERVICE_PRE_AUTHENTICATION_PATH = "/php/api/preauthenticate.php";
export const FIDO2SERVICE_AUTHENTICATION_PATH = "/php/login.php";
export const FIDO2SERVICE_PRE_AUTHORIZATION_PATH = "/php/api/preauthorize.php";
export const FIDO2SERVICE_AUTHORIZATION_PATH = "/php/api/authorize.php";
export const FIDO2SERVICE_TRANSACTIONS_PATH = "/php/transactions.php";
export const FIDO2SERVICE_LOGIN_PATH = "/php/login.php";
export const FIDO2SERVICE_RESOURCE_PATH = "/php/resource.php";
export const FIDO2SERVICE_DEREGISTER_PATH = "/php/api/deregister.php";

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

let loading_counter;
let interval;

export const showLoading = function(show){
    const dotsMove = function(){    
        let dots = document.getElementById('dots');
        switch(loading_counter){
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
    
        if(loading_counter == 2) loading_counter = 0;
        else loading_counter++;
    }
    const dotsClear = function(){
        let dots = document.getElementById('dots');
        dots.textContent = '';
    }
    if(show){
        loading_counter = 0;
        const loading = document.getElementById('loading');
        loading.classList.remove('hidden');
        interval = setInterval(dotsMove, 1000);

        const navbar = document.getElementById('nav-bar');
        navbar.classList.add('hidden');
        navbar.classList.remove('nav-bar');

        const bodylayout = document.getElementById('body-layout');
        bodylayout.classList.add('hidden');
        bodylayout.classList.remove('body-layout');
    }
    else{
        clearInterval(interval);
        dotsClear();
        interval = null;
        loading_counter = 0;

        const loading = document.getElementById('loading');
        loading.classList.add('hidden');

        const navbar = document.getElementById('nav-bar');
        navbar.classList.remove('hidden');
        navbar.classList.add('nav-bar');

        const bodylayout = document.getElementById('body-layout');
        bodylayout.classList.remove('hidden');
        bodylayout.classList.add('body-layout');
    }
} 
