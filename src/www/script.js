function request(url, data, cb){
  if (!cb) return;

  let loader = document.createElement("div");
  loader.className = "loader";
  document.body.appendChild(loader);

  formDatum = (data) ? (data instanceof FormData) ? data : new FormData(document.getElementById(data)) : new FormData();

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

function prepareMsgs(){
  errList = document.getElementById('errs');
  let transition = errList.style.transition;
  errList.innerHTML = "";
  errList.style.transition = "none";
  errList.style.opacity = 0;

  return [transition, errList];

}

function transTimeout(transition){
  setTimeout(function() {
    errList.style.transition = transition;
    errList.style.opacity = 1;
  }, 10);
}

function register(){
  request("/api/register.php", "register-form", (data)=> {

    console.log(data);

    const [transition, errList] = prepareMsgs();
    

    if (data.count == 0){
      errList.innerHTML += '<div>Your account has been created!</div><div>Please validate your email by checking here: <a href="/sec/fake-mail.php" target="_blank">Verify Mail</a></div>';
      document.getElementById("register-form").reset();
    }else{
      for(const mem in data){
        if (mem == "count") continue;
        errList.innerHTML += `<div class="err">${mem} : ${data[mem]}</div>`;
      }
    }

    transTimeout(transition);
  })
}

function sendValidateEmailRequest(){
  request("/api/sendValidationEmail.php", "validate-email-form", (data)=> {

    console.log(data);

    const [transition, errList] = prepareMsgs();
    

    if (data.count == 0){
      errList.innerHTML += '<div>Please validate your email by checking here: <a href="/sec/fake-mail.php" target="_blank">Verify Mail</a></div>';
      document.getElementById("validate-email-form").reset();
    }else{
      for(const mem in data){
        if (mem == "count") continue;
        errList.innerHTML += `<div class="err">${mem} : ${data[mem]}</div>`;
      }
    }

    transTimeout(transition);
  })
}

function login(){
  request("/api/login.php", "login-form", (data)=> {

    console.log(data);

    const [transition, errList] = prepareMsgs();
    

    if (data.count == 0){
      window.location = '/';
    }else{
      for(const mem in data){
        if (mem == "count") continue;
        errList.innerHTML += `<div class="err">${mem} : ${data[mem]}</div>`;
      }
    }

    transTimeout(transition);
  })
}

function logout() {
	request('/api/logout.php', false, function(data) {
		if(data === 0) {
			window.location = '/pages/login.php';
		}
	});
}


function deleteAccount() {
	request('/api/deleteAccount.php', false, function(data) {

    console.log(data);

    const [transition, errList] = prepareMsgs();
    

    if (data.count == 0){
      window.location = '/pages/register.php';
    }else{
      for(const mem in data){
        if (mem == "count") continue;
        errList.innerHTML += `<div class="err">${mem} : ${data[mem]}</div>`;
      }
    }

    transTimeout(transition);

  })
}

