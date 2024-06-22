document.addEventListener("DOMContentLoaded", function() {
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');

    let isDrawing = false;
    let lastX = 0;
    let lastY = 0;
    let color = '#000000';
    let brushSize = 5;

    function draw(e) {
        if (!isDrawing) return;

        ctx.strokeStyle = color;
        ctx.lineWidth = brushSize;
        ctx.lineJoin = 'round';
        ctx.lineCap = 'round';

        let currentX, currentY;
        if (e.type === 'mousemove' || e.type === 'mousedown' || e.type === 'mouseup') {
            currentX = e.offsetX;
            currentY = e.offsetY;
        } else if (e.type === 'touchmove' || e.type === 'touchstart' || e.type === 'touchend') {
            currentX = e.touches[0].clientX - canvas.getBoundingClientRect().left;
            currentY = e.touches[0].clientY - canvas.getBoundingClientRect().top;
        }

        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(currentX, currentY);
        ctx.stroke();

        lastX = currentX;
        lastY = currentY;
    }

    canvas.addEventListener('mousedown', (e) => {
        isDrawing = true;
        lastX = e.offsetX;
        lastY = e.offsetY;
    });
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', () => isDrawing = false);
    canvas.addEventListener('mouseout', () => isDrawing = false);

    canvas.addEventListener('touchstart', (e) => {
        isDrawing = true;
        lastX = e.touches[0].clientX - canvas.getBoundingClientRect().left;
        lastY = e.touches[0].clientY - canvas.getBoundingClientRect().top;
    });
    canvas.addEventListener('touchmove', draw);
    canvas.addEventListener('touchend', () => isDrawing = false);

    const colorPicker = document.getElementById('colorPicker');
    colorPicker.addEventListener('change', () => color = colorPicker.value);

    const brushSizeInput = document.getElementById('brushSize');
    brushSizeInput.addEventListener('input', () => brushSize = parseInt(brushSizeInput.value));

    const clearBtn = document.getElementById('clearBtn');
    clearBtn.addEventListener('click', () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    });

    const downloadBtn = document.getElementById('downloadBtn');
    downloadBtn.addEventListener('click', () => {
        const tempCanvas = document.createElement('canvas');
        const tempCtx = tempCanvas.getContext('2d');
        tempCanvas.width = canvas.width;
        tempCanvas.height = canvas.height;
        tempCtx.fillStyle = '#ffffff';
        tempCtx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
        tempCtx.drawImage(canvas, 0, 0);

        const dataURL = tempCanvas.toDataURL('image/jpeg', 1.0);

        const a = document.createElement('a');
        a.href = dataURL;
        a.download = 'mon_dessin.jpg';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });

    const saveBtn = document.getElementById('saveBtn');
    saveBtn.addEventListener('click', () => {
        const imageData = canvas.toDataURL('image/png');
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'image=' + encodeURIComponent(imageData),
        })
            .then(response => response.text())
            .then(data => {
                console.log(data);
                window.location.reload();
            })
            .catch(error => console.error('Error:', error));
    });

    const startDrawingBtn = document.getElementById('startDrawingBtn');
    const canvasContainer = document.querySelector('.canvas-container');
    const controlsContainer = document.querySelector('.controls-container');

    startDrawingBtn.addEventListener('click', () => {
        startDrawingBtn.style.display = 'none';
        canvasContainer.style.display = 'block';
        controlsContainer.style.display = 'block';
    });

    canvas.addEventListener('touchstart', (e) => {
        isDrawing = true;
        lastX = e.touches[0].clientX - canvas.getBoundingClientRect().left;
        lastY = e.touches[0].clientY - canvas.getBoundingClientRect().top;
        e.preventDefault();
    });
    canvas.addEventListener('touchmove', (e) => {
        draw(e);
        e.preventDefault();
    });
    canvas.addEventListener('touchend', (e) => {
        isDrawing = false;
        e.preventDefault();
    });
});