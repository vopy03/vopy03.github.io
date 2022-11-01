function addPicInput(el, from) {
	picId++;
	domEl = document.createElement("div");
	// domEl.setAttribute('onclick', "this.childNodes[1].click()")
	domEl.classList.add('other-pic-div');
	domEl.innerHTML = '<img src="img/no-image.svg" onclick="toggleImgZoom(this)">'+
		'<label for="imgInp'+from+picId+'"><p></p></label>'+
		'<input accept="image/png, image/jpeg, image/jpg" type="file" onchange="showUploadedPic(this)" name="extraPicture'+picId+'" id="imgInp'+from+picId+'" hidden />'+
  		'<span class="material-icons remove-pic-btn" onclick="remPic(this)">close</span>';
	el.previousElementSibling.appendChild(domEl);
	checkLastAddedPicInput(el, 'api');
}
function remPic(el) {
	checkLastAddedPicInput(el, 'rp');
	el.parentNode.remove();
}
function checkLastAddedPicInput(el, from) {
	var an = 0;
	if(from == 'api') {
		mDivs = el.parentNode.childNodes[5];
		btn = el.childNodes[1];
	}
	if(from == 'rp') {
		mDivs = el.parentNode.parentNode;
		btn = el.parentNode.parentNode.parentNode.childNodes[7].childNodes[1];
		an = 1; 
	}
	if(from == 'sup') {
		mDivs = el.parentNode.parentNode;
		btn = el.parentNode.parentNode.parentNode.childNodes[7].childNodes[1];
	}


	
	// console.log(mDivs);
	if(mDivs.childElementCount > 0 + an) {
		if(mDivs.parentNode.childNodes[5].childNodes[mDivs.parentNode.childNodes[5].childElementCount].childNodes[0].getAttribute('src') == 'img/no-image.svg') {
			btn.style.display = 'none';
		}
		else {
			btn.style.display = 'flex';
		}
	}
	else btn.style.display = 'flex';
	mDivs.parentNode.parentNode.parentNode.childNodes[5].childNodes[3].setAttribute('value', mDivs.parentNode.childNodes[5].childNodes[mDivs.parentNode.childNodes[5].childElementCount].childNodes[2].id.substring(9))
	// if(from == 'rp') mDivs.parentNode.parentNode.parentNode.childNodes[5].childNodes[3].setAttribute('value', 0);
}

function showUploadedPic(el) {
	const [file] = el.files
    if (file) {
    	el.parentNode.childNodes[0].src = URL.createObjectURL(file);
    	el.parentNode.childNodes[1].childNodes[0].innerHTML = file.name;
    }
	checkLastAddedPicInput(el, 'sup');
}
function showUPMain(el) {
	const [file] = el.files
    if (file) {
    	el.parentNode.childNodes[3].childNodes[1].src = URL.createObjectURL(file);
    }
}
function toggleImgZoom(el) {
	if(el.getAttribute('src') != 'img/no-image.svg') el.classList.toggle('img-active');
}

function verifyFileSize(el, msg){
   /* Attached file size check. Will Bontrager Software LLC, https://www.willmaster.com */
   var MaxSizeInBytes = 2097152;
   if( el.files && el.files.length == 1 && el.files[0].size > MaxSizeInBytes )
   {
      alert(msg + parseInt(MaxSizeInBytes/1024/1024) + "MB");
      return false;
   }
   return true;
} // function VerifyUploadSizeIsOK()

// urlToObject("localhost/lp/img/services/bdsdfgesf/tcp_dl.png");