<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title></title> 
    
</head>
<body>
<img src="overtime_logo.png" id="logo">

  <div id="main">
    <div class="block" id="block1"> <input type="text" id="input1"  onchange="funkcja('input1', this.value)"><p>A1</p></div>
    <div class="block" id="block2"> <input type="text" id="input2"  onchange="funkcja('input2', this.value)"><p>B1</p></div>
    <div class="block" id="block3"> <input type="text" id="input3"  onchange="funkcja('input3', this.value)"><p>C1</p></div>
    <div class="block" id="block4"> <input type="text" id="input4"  onchange="funkcja('input4', this.value)"><p>D1</p></div>
    <div class="block" id="block5"> <input type="text" id="input5"  onchange="funkcja('input5', this.value)"><p>E1</p></div>
    <div class="block" id="block6"> <input type="text" id="input6"  onchange="funkcja('input6', this.value)"><p>A2</p></div>
    <div class="block" id="block7"> <input type="text" id="input7"  onchange="funkcja('input7', this.value)"><p>B2</p></div>
    <div class="block" id="block8"> <input type="text" id="input8"  onchange="funkcja('input8', this.value)"><p>C2</p></div>
    <div class="block" id="block9"> <input type="text" id="input9"  onchange="funkcja('input9', this.value)"><p>D2</p></div>
    <div class="block" id="block10"><input type="text" id="input10" onchange="funkcja('input10', this.value)"><p>E2</p></div>
    <div class="block" id="block11"><input type="text" id="input11" onchange="funkcja('input11', this.value)"><p>A3</p></div>
    <div class="block" id="block12"><p>B3</p><h1>GOLD</h1><h3>OLIVE jest w D1. GRAY jest gdzieś w piątym rzędzie.</h3></div>
    <div class="block" id="block13"><input type="text" id="input13" onchange="funkcja('input13', this.value)"><p>C3</p></div>
    <div class="block" id="block14"><input type="text" id="input14" onchange="funkcja('input14', this.value)"><p>D3</p></div>
    <div class="block" id="block15"><input type="text" id="input15" onchange="funkcja('input15', this.value)"><p>E3</p></div>
    <div class="block" id="block16"><input type="text" id="input16" onchange="funkcja('input16', this.value)"><p>A4</p></div>
    <div class="block" id="block17"><input type="text" id="input17" onchange="funkcja('input17', this.value)"><p>B4</p></div>
    <div class="block" id="block18"><input type="text" id="input18" onchange="funkcja('input18', this.value)"><p>C4</p></div>
    <div class="block" id="block19"><input type="text" id="input19" onchange="funkcja('input19', this.value)"><p>D4</p></div>
    <div class="block" id="block20"><input type="text" id="input20" onchange="funkcja('input20', this.value)"><p>E4</p></div>
    <div class="block" id="block21"><input type="text" id="input21" onchange="funkcja('input21', this.value)"><p>A5</p></div>
    <div class="block" id="block22"><input type="text" id="input22" onchange="funkcja('input22', this.value)"><p>B5</p></div>
    <div class="block" id="block23"><input type="text" id="input23" onchange="funkcja('input23', this.value)"><p>C5</p></div>
    <div class="block" id="block24"><input type="text" id="input24" onchange="funkcja('input24', this.value)"><p>D5</p></div>
    <div class="block" id="block25"><input type="text" id="input25" onchange="funkcja('input25', this.value)"><p>E5</p></div>  
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

  fetch('sprawdz4.php', {
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