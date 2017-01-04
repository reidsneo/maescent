/*Tests to see if ajax is available then Creates an Ajax request. 
*Params: url - the api url
*        type - the type of request (get, post). Default is get
*        callback - function to process the ajax response
*/
function makeRequest(url, type, callback) {
type = typeof type !== 'undefined' ? type : 'GET';
var httpRequest;
if (window.XMLHttpRequest) { // Mozilla, Safari, ...
   httpRequest = new XMLHttpRequest();
} else if (window.ActiveXObject) { // IE
  try {
    httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
  } 
  catch (e) {
    try {
      httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
    } 
    catch (e) {}
  }
}

if (!httpRequest) {
  alert('Giving up :( Cannot create an XMLHTTP instance');
  return false;
}
httpRequest.onreadystatechange = function(){
  try {
    if (httpRequest.readyState === 4) {
      if (httpRequest.status === 200) {
        //Should just return the json
        var response = JSON.parse(httpRequest.responseText);
        // console.log(response);
        return callback(response);
      } else {
        alert('There was a problem with the request.');
      }
    }
  } catch(e) {
    alert('Caught Exception: ' + e.description);
  }
}
httpRequest.open(type, url);
//httpRequest.setRequestHeader('Content-Type', 'application/xml');
httpRequest.send();
}