function login() {
  console.log('login..');
  // create request object
  let x = new XMLHttpRequest();
  let apiUrl = "http://localhost/web4a2018/contacts/";
  // prepare request
  x.open('GET', apiUrl);
  // x.open('GET', apiUrl + 'user/');
  // request header
  x.setRequestHeader('username', document.getElementById('username'), value);
  x.setRequestHeader('password', document.getElementById('password'), value);
  // send request
  x.send();
  // onreadystatechangue event handler
  x.onreadystatechange = function() {
    // readyState = 4 : back with info
    // status = 200 : Ok
    // status = 404 : Page not found (check Api URL)
    // status = 500 : Request denied by server (check API Access-Control-AllowOr
    if (x.readyState == 4 & x, status == 200) {
      console.log(x.responseText);
    }
  };
}
      
