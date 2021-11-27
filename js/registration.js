

function submitForm(intent){
    if(intent === "registration"){
        let firstname = document.getElementById("firstname").value;
        let lastname = document.getElementById("lastname").value;
        let username = document.getElementById("username").value;
        let displayname = document.getElementById("displayname").value;
        console.log(firstname);
        console.log(lastname);
        console.log(username);
        console.log(displayname);
    }
}