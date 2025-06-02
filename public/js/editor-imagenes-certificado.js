// Editor de imágenes para certificados
class CertificadoImageEditor {
    constructor(options = {}) {
        this.options = {
            headerContainer: options.headerContainer || '#header-image-editor',
            footerContainer: options.footerContainer || '#footer-image-editor',
            onSave: options.onSave || function() {},
            headerImages: options.headerImages || [],
            footerImages: options.footerImages || []
        };
        
        this.headerImages = [];
        this.footerImages = [];
        
        this.init();
    }
    
    init() {
        console.log('Inicializando contenedores de imágenes...');
        // Inicializar contenedores
        this.initContainer(this.options.headerContainer, 'header');
        this.initContainer(this.options.footerContainer, 'footer');
        
        // Cargar imágenes si existen
        if (this.options.headerImages && this.options.headerImages.length > 0) {
            this.loadImages(this.options.headerImages, 'header');
        }
        
        if (this.options.footerImages && this.options.footerImages.length > 0) {
            this.loadImages(this.options.footerImages, 'footer');
        }
    }
    
    initContainer(selector, type) {
        const container = document.querySelector(selector);
        if (!container) {
            console.error(`Contenedor ${selector} no encontrado`);
            return;
        }
        
        // Limpiar el contenedor
        container.innerHTML = '';
        
        // Crear área de edición
        const editorArea = document.createElement('div');
        editorArea.className = `image-editor-area ${type}-editor-area`;
        editorArea.style.position = 'relative';
        editorArea.style.width = '100%';
        editorArea.style.height = type === 'header' ? '150px' : '100px';
        editorArea.style.border = '1px dashed #ccc';
        editorArea.style.marginBottom = '10px';
        editorArea.style.backgroundColor = '#f9f9f9';
        editorArea.style.overflow = 'hidden';
        
        // Añadir texto de ayuda
        const helpText = document.createElement('div');
        helpText.className = 'help-text';
        helpText.style.position = 'absolute';
        helpText.style.top = '50%';
        helpText.style.left = '50%';
        helpText.style.transform = 'translate(-50%, -50%)';
        helpText.style.color = '#999';
        helpText.style.pointerEvents = 'none';
        helpText.textContent = type === 'header' ? 'Área del encabezado' : 'Área del pie de página';
        
        editorArea.appendChild(helpText);
        container.appendChild(editorArea);
        
        // Crear barra de herramientas
        const toolbar = document.createElement('div');
        toolbar.className = 'editor-toolbar';
        toolbar.style.marginBottom = '15px';
        
        // Botón para añadir imagen
        const addButton = document.createElement('button');
        addButton.type = 'button'; // Importante para evitar submit del formulario
        addButton.className = 'btn btn-primary btn-sm me-2';
        addButton.innerHTML = '<i class="fa fa-plus"></i> Añadir Imagen';
        
        // Input oculto para seleccionar imágenes
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/*';
        fileInput.style.display = 'none';
        fileInput.id = `${type}-image-input`;
        
        // Manejar clic en botón de añadir
        addButton.addEventListener('click', (e) => {
            e.preventDefault(); // Prevenir comportamiento predeterminado
            fileInput.click();
        });
        
        // Manejar selección de archivo
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                this.uploadImage(file, type);
            }
        });
        
        toolbar.appendChild(addButton);
        toolbar.appendChild(fileInput);
        
        container.appendChild(toolbar);
        
        // Guardar referencia al área de edición
        this[`${type}EditorArea`] = editorArea;
    }
    
    uploadImage(file, type) {
        // Crear FormData para enviar el archivo
        const formData = new FormData();
        formData.append('imagen', file);
        formData.append('tipo', type); // Indicar si es header o footer
        
        // Mostrar indicador de carga
        Swal.fire({
            title: 'Subiendo imagen',
            text: 'Por favor espere...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Enviar la imagen al servidor
        fetch(_URL + "/ajs/certificado/subir-imagen", {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            
            if (data.success) {
                // Añadir la imagen al editor
                this.addImage(data.imageUrl, type);
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message || 'No se pudo subir la imagen',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error al subir la imagen:', error);
            Swal.fire({
                title: 'Error',
                text: 'No se pudo conectar con el servidor',
                icon: 'error'
            });
        });
    }
    
    addImage(src, type, x = 10, y = 10, width = 100, height = 50) {
        const editorArea = this[`${type}EditorArea`];
        if (!editorArea) return;
        
        // Crear contenedor de la imagen
        const imgContainer = document.createElement('div');
        imgContainer.className = 'draggable-image';
        imgContainer.style.position = 'absolute';
        imgContainer.style.left = `${x}px`;
        imgContainer.style.top = `${y}px`;
        imgContainer.style.cursor = 'move';
        imgContainer.style.zIndex = '10';
        
        // Crear la imagen
        const img = document.createElement('img');
        img.src = src;
        img.style.width = `${width}px`;
        img.style.height = `${height}px`;
        img.style.display = 'block';
        
        // Añadir controles
        const controls = document.createElement('div');
        controls.className = 'image-controls';
        controls.style.position = 'absolute';
        controls.style.top = '0';
        controls.style.right = '0';
        controls.style.backgroundColor = 'rgba(0,0,0,0.5)';
        controls.style.borderRadius = '3px';
        controls.style.padding = '2px';
        controls.style.display = 'none';
        
        // Botón de eliminar
        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className = 'btn btn-danger btn-sm';
        deleteBtn.innerHTML = '<i class="fa fa-trash"></i>';
        deleteBtn.style.padding = '2px 5px';
        deleteBtn.style.fontSize = '10px';
        
        deleteBtn.addEventListener('click', () => {
            editorArea.removeChild(imgContainer);
            
            // Eliminar de la lista de imágenes
            const imageList = type === 'header' ? this.headerImages : this.footerImages;
            const index = imageList.findIndex(img => img.src === src);
            if (index !== -1) {
                imageList.splice(index, 1);
            }
        });
        
        controls.appendChild(deleteBtn);
        
        // Añadir controlador de redimensionamiento
        const resizer = document.createElement('div');
        resizer.className = 'resizer';
        resizer.style.position = 'absolute';
        resizer.style.right = '0';
        resizer.style.bottom = '0';
        resizer.style.width = '10px';
        resizer.style.height = '10px';
        resizer.style.backgroundColor = '#0047ba';
        resizer.style.cursor = 'se-resize';
        
        // Añadir elementos al DOM
        imgContainer.appendChild(img);
        imgContainer.appendChild(controls);
        imgContainer.appendChild(resizer);
        editorArea.appendChild(imgContainer);
        
        // Mostrar controles al pasar el mouse
        imgContainer.addEventListener('mouseenter', () => {
            controls.style.display = 'block';
        });
        
        imgContainer.addEventListener('mouseleave', () => {
            controls.style.display = 'none';
        });
        
        // Hacer la imagen arrastrable
        this.makeElementDraggable(imgContainer, editorArea);
        
        // Hacer la imagen redimensionable
        this.makeElementResizable(resizer, img);
        
        // Guardar referencia a la imagen
        const imageData = {
            src: src,
            x: x,
            y: y,
            width: width,
            height: height,
            element: imgContainer
        };
        
        if (type === 'header') {
            this.headerImages.push(imageData);
        } else {
            this.footerImages.push(imageData);
        }
    }
    
    makeElementDraggable(element, container) {
        let offsetX = 0, offsetY = 0, isDragging = false;
        
        element.addEventListener('mousedown', (e) => {
            // Solo iniciar arrastre si el clic es en la imagen o en el contenedor (no en los controles)
            if (e.target.tagName === 'IMG' || e.target === element) {
                isDragging = true;
                offsetX = e.clientX - element.getBoundingClientRect().left;
                offsetY = e.clientY - element.getBoundingClientRect().top;
                e.preventDefault();
            }
        });
        
        document.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            
            const containerRect = container.getBoundingClientRect();
            let newX = e.clientX - containerRect.left - offsetX;
            let newY = e.clientY - containerRect.top - offsetY;
            
            // Limitar dentro del contenedor
            newX = Math.max(0, Math.min(newX, containerRect.width - element.offsetWidth));
            newY = Math.max(0, Math.min(newY, containerRect.height - element.offsetHeight));
            
            element.style.left = `${newX}px`;
            element.style.top = `${newY}px`;
            
            // Actualizar posición en el objeto de datos
            const imageList = this.headerImages.concat(this.footerImages);
            const imageData = imageList.find(img => img.element === element);
            if (imageData) {
                imageData.x = newX;
                imageData.y = newY;
            }
        });
        
        document.addEventListener('mouseup', () => {
            isDragging = false;
        });
    }
    
    makeElementResizable(resizer, imgElement) {
        let startX, startY, startWidth, startHeight, isResizing = false;
        
        resizer.addEventListener('mousedown', (e) => {
            isResizing = true;
            startX = e.clientX;
            startY = e.clientY;
            startWidth = parseInt(imgElement.style.width, 10);
            startHeight = parseInt(imgElement.style.height, 10);
            e.preventDefault();
            e.stopPropagation();
        });
        
        document.addEventListener('mousemove', (e) => {
            if (!isResizing) return;
            
            const width = startWidth + (e.clientX - startX);
            const height = startHeight + (e.clientY - startY);
            
            if (width > 20 && height > 20) {
                imgElement.style.width = `${width}px`;
                imgElement.style.height = `${height}px`;
                
                // Actualizar dimensiones en el objeto de datos
                const container = imgElement.parentElement;
                const imageList = this.headerImages.concat(this.footerImages);
                const imageData = imageList.find(img => img.element === container);
                
                if (imageData) {
                    imageData.width = width;
                    imageData.height = height;
                }
            }
        });
        
        document.addEventListener('mouseup', () => {
            isResizing = false;
        });
    }
    
    getConfiguration() {
        return {
            header: this.headerImages.map(img => ({
                src: img.src,
                x: img.x,
                y: img.y,
                width: img.width,
                height: img.height
            })),
            footer: this.footerImages.map(img => ({
                src: img.src,
                x: img.x,
                y: img.y,
                width: img.width,
                height: img.height
            }))
        };
    }
    
    loadConfiguration(config) {
        if (!config) return;
        
        // Limpiar imágenes existentes
        this.clearImages('header');
        this.clearImages('footer');
        
        // Cargar imágenes de encabezado
        if (config.header && Array.isArray(config.header)) {
            this.loadImages(config.header, 'header');
        }
        
        // Cargar imágenes de pie de página
        if (config.footer && Array.isArray(config.footer)) {
            this.loadImages(config.footer, 'footer');
        }
    }
    
    loadImages(images, type) {
        images.forEach(img => {
            this.addImage(img.src, type, img.x, img.y, img.width, img.height);
        });
    }
    
    clearImages(type) {
        const editorArea = this[`${type}EditorArea`];
        if (!editorArea) return;
        
        // Eliminar todas las imágenes del área de edición
        const images = editorArea.querySelectorAll('.draggable-image');
        images.forEach(img => editorArea.removeChild(img));
        
        // Limpiar array de imágenes
        if (type === 'header') {
            this.headerImages = [];
        } else {
            this.footerImages = [];
        }
    }
}