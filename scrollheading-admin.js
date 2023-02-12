document.addEventListener("DOMContentLoaded", function() {
var input = document.querySelector("#scrollheading-input-admin");
input.value = scrollheading_text.text;
input.addEventListener("change", function() {
var text = input.value;
var xhr = new XMLHttpRequest();
xhr.open("POST", ajaxurl, true);
xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
xhr.onreadystatechange = function() {
if (xhr.readyState === 4 && xhr.status === 200) {
document.querySelector("#scrollheading-input").value = text;
}
};
xhr.send("action=scrollheading_update_text&scrollheading_text=" + text);
});
});