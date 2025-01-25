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
            height: 80vh;
            width: 70vw; 
            background-color: #ede7b9;
            border-radius: 8px; 
        }
    </style>
</head>
<body>
  <img src="overtime_logo.png" id="logo">
  <div id="mainn">
    <div class="block" id="block1"> <input type="text" id="input1"  onchange="funkcja('input1', this.value)"><p>A1</p></div>
    <div class="block" id="block2"> <input type="text" id="input2"  onchange="funkcja('input2', this.value)"><p>B1</p></div>
    <div class="block" id="block3"> <input type="text" id="input3"  onchange="funkcja('input3', this.value)"><p>C1</p></div>
    <div class="block" id="block4"> <input type="text" id="input4"  onchange="funkcja('input4', this.value)"><p>D1</p></div>
    <div class="block" id="block5"> <input type="text" id="input5"  onchange="funkcja('input5', this.value)"><p>A2</p></div>
    <div class="block" id="block6"> <p>B2</p> <h1>SELF-REPORT</h1> <h3>Wszystkie mapy znajdują się w przedostatniej kolumnie. THE FUNGLE nie przylega do tej komórki.</h3></div>
    <div class="block" id="block7"> <input type="text" id="input7"  onchange="funkcja('input7', this.value)"><p>C2</p></div>
    <div class="block" id="block8"> <input type="text" id="input8"  onchange="funkcja('input8', this.value)"><p>D2</p></div>
    <div class="block" id="block9"> <input type="text" id="input9"  onchange="funkcja('input9', this.value)"><p>A3</p></div>
    <div class="block" id="block10"><input type="text" id="input10" onchange="funkcja('input10', this.value)"><p>B3</p></div>
    <div class="block" id="block11"><input type="text" id="input11" onchange="funkcja('input11', this.value)"><p>C3</p></div>
    <div class="block" id="block12"><input type="text" id="input12" onchange="funkcja('input12', this.value)"><p>D3</p></div>
    <div class="block" id="block13"><input type="text" id="input13" onchange="funkcja('input13', this.value)"><p>A4</p></div>
    <div class="block" id="block14"><input type="text" id="input14" onchange="funkcja('input14', this.value)"><p>B4</p></div>
    <div class="block" id="block15"><input type="text" id="input15" onchange="funkcja('input15', this.value)"><p>C4</p></div>
    <div class="block" id="block16"><input type="text" id="input16" onchange="funkcja('input16', this.value)"><p>D4</p></div>
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

 
  fetch('sprawdz4x4.php', {
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