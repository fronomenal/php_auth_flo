function request(url, data, cb){
  if (!cb) return;

  let loader = document.createElement("div");
  loader.className = "loader";
  document.getElementById("register-form").appendChild(loader);

  formDatum = (data) ? ((data instanceof FormData) ? data : new FormData(document.getElementById(data))) : undefined

  csrft = document.querySelector("meta[name='csrf_token']");

  if(formDatum && csrft){
    formDatum.append('csrf_token', csrft.getAttribute('content'));
  }

  fetch(
    url, {
    method: 'POST',
    body: formDatum
  }).then(res => {
    loader.remove();
    return res.json();
  }).then(cb).catch(err => console.log(err));

}

function register(){
  request("../api/register.php", "register-form", (data)=> {

    console.log(data);

    errList = document.getElementById('errs');
		let transition = errList.style.transition;
    errList.innerHTML = "";
		errList.style.transition = "none";
		errList.style.opacity = 0;

    if (data.count == 0){
      errList.innerHTML += '<div>Your account has been created!</div><div>Please validate your email by checking here: <a href="/sec/fake-mail" target="_blank">Verify Mail</a>.</div>';
      document.getElementById("register-form").reset();
    }else{
      for(const mem in data){
        if (mem == "count") continue;
        errList.innerHTML += `<div class="err">${mem} : ${data[mem]}</div>`;
      }
    }

    setTimeout(function() {
      errList.style.transition = transition;
      errList.style.opacity = 1;
    }, 10);
  })
}

function sendValidateEmailRequest(){
  request("../api/sendValidationEmail.php", "validate-email-form", (data)=> {

    console.log(data);

    errList = document.getElementById('errs');
		let transition = errList.style.transition;
    errList.innerHTML = "";
		errList.style.transition = "none";
		errList.style.opacity = 0;

    if (data.count == 0){
      errList.innerHTML += "<div>Email Sent... Check your email and click the link in the email to validate your email.</div>";
    }else{
      for(const mem in data){
        if (mem == "count") continue;
        errList.innerHTML += `<div class="err">${mem} : ${data[mem]}</div>`;
      }
    }

    setTimeout(function() {
      errList.style.transition = transition;
      errList.style.opacity = 1;
    }, 10);
  })
}