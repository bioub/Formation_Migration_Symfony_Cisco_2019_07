setInterval(() => {
    var c = Math.floor(Math.random() * 256);
    document.body.style.backgroundColor = `rgb(${c}, ${c}, ${c})`;
}, 1000);