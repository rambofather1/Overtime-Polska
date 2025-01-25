<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title></title> 

    <style>
        #mainn {
            display: flex; 
            justify-content: center; 
            align-items: center; 
            flex-wrap: wrap; 
            height: 40vh;
            width: 40vw; 
            background-color: #c8f5bc;
            border-radius: 8px; 
        }
    </style>
    
</head>
<body>
  <img src="overtime_logo.png" id="logo">
  <div id="mainn">
    <div class="block" id="block1"> <p>A1</p> <h1>JEDEN</h1> <h3>W B2 znajduje się cyfra o dwa większa niż w nazwie tej komórki.</h3></div>
    <div class="block" id="block2"> <input type="text" id="input2"  onchange="funkcja('input2', this.value)"><p>B1</p></div> 
    <div class="block" id="block3"> <input type="text" id="input3"  onchange="funkcja('input3', this.value)"><p>A2</p></div>
    <div class="block" id="block4"> <input type="text" id="input4"  onchange="funkcja('input4', this.value)"><p>B2</p></div>
  </div>
      
  <script>
let cooldownActive = false;

function funkcja(nazwaInputa, value) {
  const inputElements = document.querySelectorAll('input'); 
  value = value.toUpperCase();


  if (cooldownActive) return;


  const formData = new FormData();
  formData.append('inputValue', value);
  formData.append('inputId', nazwaInputa);

 
  fetch('sprawdz2x2.php', {
    method: 'POST',
    body: formData,
  })
    .then(response => response.json())
    .then(data => {
      if (data.result === true) {
        console.log('Wartość poprawna! 🎉');

        const inputElement = document.getElementById(nazwaInputa);
        const parentBlock = inputElement.parentElement;

        inputElement.remove();

        const h1 = document.createElement('h1');
        h1.textContent = data.h1Text;

        const h3 = document.createElement('h3');
        h3.textContent = data.h3Text;

        parentBlock.appendChild(h1);
        parentBlock.appendChild(h3);
      } else {
        console.log('Wartość niepoprawna. 😕');

        cooldownActive = true;

        const inputElement = document.getElementById(nazwaInputa);
        inputElement.classList.add('input-error'); 
        inputElements.forEach(input => {
          input.disabled = true;
        });

        setTimeout(() => {
          inputElements.forEach(input => {
            input.classList.remove('input-error');
            input.disabled = false;
          });
          cooldownActive = false;
        }, 10000);
      }
    })
    .catch(error => {
      console.error('Wystąpił błąd:', error);
    });
}
  </script>
      
</body>
</html>