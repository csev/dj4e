<h1>JavaScript Autograder</h1>

<script>
// var baseurl = "https://djtutorial.dj4e.com/polls4/";
var baseurl = "http://localhost:9000/";
</script>

Url to test:
<input type="text" name="baseurl" style="width:60%;" value="http://localhost:9000"
onchange="newUrl(this.value);return false;"
/></br>

<button onclick="doNextJson();" id="nextjson" disabled>Next JSON</button>
<span id="stepinfo">
Placeholder
</span>

<br/>
<center>
<script>
document.write('<iframe style="width:95%; height:600;" id="myframe"');
document.write('src="'+baseurl+'">');
document.write('</iframe>');
</script>
</center>


<script>
var currenturl = baseurl;

var currentStep;

window.addEventListener(
  "message",
  (event) => {
    console.log('in parent', event, currentStep);

    const bodystr = JSON.stringify({
          step: currentStep,
          response: event.data,
    });

    console.log("Body ", bodystr);

    fetch('fw_grader.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: bodystr,
      })
      .then(response => response.json())
      .then(data => {
        // Handle the response data
        console.log('Next Step', data);
        currentStep = data;
     })
     .catch(error => {
       // Handle any errors
       console.error('Error:', error);
     });
  },
  false,
);

function newUrl(newurl) {
    console.log("Switching to new url", newurl);
    baseurl = newurl;
    currenturl = baseurl;
        document.getElementById('myframe').src = currenturl;
    currentStep = -1;
        moveToNextStep();
}

function doNextStep() {

    if ( currentStep >= (steps.length-1) ) {
        document.getElementById('stepinfo').textContent = 'Test complete';
        document.getElementById('nextstep').disabled = true;
        return;
    }
        document.getElementById('nextstep').disabled = false;
        const step = steps[currentStep];
        console.log(step);
        if ( step.command == 'switchurl' ) {
                currenturl = (baseurl + step.text).replace('/\/\//','/');
                console.log('Switching to', currenturl);
                document.getElementById('myframe').src = currenturl;
                moveToNextStep();
                return;
        }
    console.log('Sending...', step, currenturl);
    document.getElementById('myframe').contentWindow.postMessage(step, currenturl);
    console.log('sent...');
}

function doNextJson() {
    console.log(currentStep)
    console.log('Sending...', currentStep, currenturl);
    document.getElementById('myframe').contentWindow.postMessage(currentStep, currenturl);
    console.log('sent...');
}

console.log("loading the first step");
// Get the first currentStep
fetch('fw_grader.php') // api for the get request
    .then(response => response.json())
    .then(step => {
        console.log('First step', step)
        currentStep = step;
        document.getElementById('nextjson').disabled = false;
    });



</script>
