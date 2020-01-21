function cin(e) {
  e.style.backgroundColor='#D4C0A1';
}

function cout(e) {
  e.style.backgroundColor='#F1E0C6';
}

function greset(e) {
  if (e.value == '' || e.value == ' ')
    e.value = e.defaultValue;
}

function gwipe(e) {
  if (e.value == e.defaultValue) e.value='';
}

function gmore() {
  if(state < 6) {
    state++;
	e = document.getElementById("list");
	x = document.createElement("input");
	x.type = "text";
	x.name = "g"+state;
	x.setAttribute('class',"blk txt");
	x.setAttribute('size',30);
	x.setAttribute('maxlength',30);
	x.value = "";
	e.appendChild(x);
  }
}

function gless() {
  if(state > 1) {
	e = document.getElementById("list");
	e.removeChild(e.lastChild);
    state--;
  }
}


