let chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';
let lookup = new Uint8Array(256);
for (let i = 0; i < chars.length; i++) {
  lookup[chars.charCodeAt(i)] = i;
}

let encode = function (arraybuffer) {
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
  
let decode = function (base64string) {
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



function challengeToBuffer(input) {
    input = JSON.parse(input);
    console.log(input.Response.challenge);
    input.Response.challenge = decode(input.Response.challenge);
    if(typeof input.Response.user !== 'undefined') {
        input.Response.user.id = decode(input.Response.user.id);
    }
  
    if (input.Response.excludeCredentials) {
        for (let i = 0; i < input.Response.excludeCredentials.length; i++) {
            input.Response.excludeCredentials[i].id = input.Response.excludeCredentials[i].id.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
            input.Response.excludeCredentials[i].id = decode(input.Response.excludeCredentials[i].id);
        }
    }
    
    if (input.Response.allowCredentials) {
      for (let i = 0; i < input.Response.allowCredentials.length; i++) {
        input.Response.allowCredentials[i].id = input.Response.allowCredentials[i].id.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
        input.Response.allowCredentials[i].id = decode(input.Response.allowCredentials[i].id);
      }
    }
    return input;
}

function responseToBase64(input) {
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


function createCredentials(intent, challenge){
    let challengeBuffer = challengeToBuffer(challenge);
    let credentialsContainer = window.navigator;
    credentialsContainer.credentials.create({ publicKey: challengeBuffer.Response })
    .then((credResp) => {
        let credResponse = responseToBase64(credResp);
        credResponse.intent = intent;
        console.log(credResponse);
        return credResponse;
    })
    .catch((err) => {
        alert(err);
    })
}

function callFIDO2Token(intent,challenge) {
    let challengeBuffer = challengeToBuffer(challenge);
    let credentialsContainer = window.navigator;
    if(intent=="registration"){
      credentialsContainer.credentials.create({ publicKey: challengeBuffer.Response })
      .then(credResp => {
        let credResponse = responseToBase64(credResp);
        credResponse.intent = intent;
        $.post('/submitChallengeResponse',  credResponse)
        .done(regResponse => onResult(intent,regResponse))
        .fail((jqXHR, textStatus, errorThrown) => {
          console.log(jqXHR, textStatus, errorThrown);
        });
      })
      .catch(error => {
        alert(error);
      });
    } else if (intent=="authentication"){
      credentialsContainer.credentials.get({ publicKey: challengeBuffer.Response })
        .then(credResp => {
          let credResponse = responseToBase64(credResp);
          credResponse.intent = intent;
          $.post('/submitChallengeResponse', credResponse)
          .done(authResponse => onResult(intent,authResponse))
          .fail((jqXHR, textStatus, errorThrown) => {
            alert(jqXHR, textStatus, errorThrown);
          });
        })
        .catch(error => {
          alert(error);
        });
      }
    }
    function onResult(intent,response){
      if(intent=="registration"){
        if(!response.Response.toString().toLowerCase().includes("error")){
          window.location.replace(window.location.protocol + "//" + window.location.host + "/login");
        } else {
          alert(response.Response);
        }
    } else if(intent=="authentication"){
      if(response.Response.toString().includes("Successfully processed sign response")){
        window.location.replace(window.location.protocol + "//" + window.location.host + "/dashboard");
      } else {
        alert(response.Response);
      }
    }
  }