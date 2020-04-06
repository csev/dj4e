// https://stackoverflow.com/questions/9333914/get-content-inside-script-as-text
// https://stackoverflow.com/a/34854903
// https://stackoverflow.com/a/52058753

function renderPRE( script, codeScriptName ){
  var isIE = !document.currentScript;
  if (isIE) return;

  var jsCode = script.innerHTML.trim();
  // escape angled brackets between two _ESCAPE_START_ and _ESCAPE_END_ comments
  let textsToEscape = jsCode.match(new RegExp("// _ESCAPE_START_([^]*?)// _ESCAPE_END_", 'mg'));
  if (textsToEscape) {
    textsToEscape.forEach(textToEscape => {
      jsCode = jsCode.replace(textToEscape, textToEscape.replace(/</g, "&lt")
        .replace(/>/g, "&gt")
        .replace("// _ESCAPE_START_", "")
        .replace("// _ESCAPE_END_", "")
        .trim());
    });
  } else {
      jsCode = jsCode.replace(/</g, "&lt").replace(/>/g, "&gt").trim();
  }
  script.insertAdjacentHTML('afterend', "<hr/><pre class='language-js'><code>" + jsCode + "</code></pre>");
}

renderPRE(document.getElementsByTagName("script")[0]);

