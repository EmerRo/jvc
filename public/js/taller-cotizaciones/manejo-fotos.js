$(document).ready(() => {
  // Variables globales
  const selectedFiles = []

  // Manejador de selección de archivos
  $("#imageInput").on("change", function (e) {
    const files = Array.from(this.files)
    const equipoActivo = vueApp.equipoActivo

    // Asegurarnos que el equipo activo tenga un array de fotos
    if (!vueApp.equiposPreAlerta[equipoActivo].fotos) {
      vueApp.equiposPreAlerta[equipoActivo].fotos = []
    }

    // Verificar el límite de imágenes para el equipo actual
    if (vueApp.equiposPreAlerta[equipoActivo].fotos.length + files.length > 12) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "No se pueden agregar más de 12 fotos para este equipo",
      })
      this.value = ""
      return
    }

    // Procesar cada archivo
    files.forEach((file) => {
      if (!file.type.startsWith("image/")) {
        Swal.fire({
          icon: "error",
          title: "Tipo de archivo no válido",
          text: "Por favor, selecciona solo archivos de imagen.",
        })
        return
      }

      const reader = new FileReader()
      reader.onload = (e) => {
        // Agregar la vista previa para el equipo actual
        addImagePreview(e.target.result, vueApp.equiposPreAlerta[equipoActivo].fotos.length)

        // Guardar el archivo en el array de fotos del equipo actual
        vueApp.equiposPreAlerta[equipoActivo].fotos.push(file)
      }
      reader.readAsDataURL(file)
    })

    this.value = ""
  })

  // Función para guardar fotos
  $("#btnGuardarFotos").click(() => {
    const equipoActivo = vueApp.equipoActivo

    if (!vueApp.equiposPreAlerta[equipoActivo].fotos || vueApp.equiposPreAlerta[equipoActivo].fotos.length === 0) {
      Swal.fire({
        icon: "warning",
        title: "Advertencia",
        text: "Por favor, selecciona al menos una foto para este equipo",
      })
      return
    }

    Swal.fire({
      icon: "success",
      title: "¡Éxito!",
      text: "Fotos agregadas correctamente para el equipo " + (equipoActivo + 1),
    })
    $("#modalFotos").modal("hide")

    // Actualizar la vista previa
    updatePhotoPreview()
  })

  // Función para construir la URL de la imagen
  function buildImageUrl(fileName) {
    // Asegurarse de que la URL base termine con una barra
    const baseUrl = _URL.endsWith("/") ? _URL : _URL + "/"
    return `${baseUrl}public/assets/img/cotizaciones/${fileName}`
  }

  // Función para agregar vista previa de imagen
  function addImagePreview(src, index, isExisting = false, fileName = "") {
    const preview = document.getElementById("imagePreview")
    const div = document.createElement("div")
    div.className = "preview-item position-relative"
    div.setAttribute("data-index", index)

    const img = new Image()
    img.className = "preview-image"

    // Si es una foto existente, construir la URL completa
    if (isExisting) {
      img.src = buildImageUrl(fileName)
    } else {
      img.src = src
    }
    img.alt = "Vista previa"
    img.style.width = "150px"
    img.style.height = "150px"
    img.style.objectFit = "cover"
    img.style.borderRadius = "4px"

    // Manejar errores de carga de imagen
    img.onerror = () => {
      console.error(`Error al cargar la imagen: ${img.src}`)
      img.src = "/placeholder.svg?height=150&width=150"
    }

    const deleteBtn = document.createElement("button")
    deleteBtn.className = "remove-btn position-absolute top-0 end-0 btn btn-danger btn-sm"
    deleteBtn.innerHTML = "×"
    deleteBtn.style.margin = "5px"
    deleteBtn.onclick = () => {
      removeImage(index)
    }

    div.appendChild(img)
    div.appendChild(deleteBtn)
    preview.appendChild(div)
  }

  // Función para remover imagen
// Función para remover imagen
function removeImage(index) {
  const equipoActivo = vueApp.equipoActivo;
  const foto = vueApp.equiposPreAlerta[equipoActivo].fotos[index];
  
  // Si es una foto existente (tiene nombre_foto)
  if (foto && foto.nombre_foto) {
    Swal.fire({
      title: '¿Estás seguro?',
      text: "La imagen será eliminada permanentemente",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        // Hacer la llamada AJAX para eliminar la foto
        $.ajax({
          url: _URL + '/ajs/taller/cotizaciones/eliminar-foto',
          type: 'POST',
          data: {
            id_cotizacion: vueApp.venta.id_cotizacion,
            nombre_foto: foto.nombre_foto,
            equipo_index: equipoActivo
          },
          success: function(response) {
            if (response.success) {
              // Eliminar del array de fotos
              vueApp.equiposPreAlerta[equipoActivo].fotos.splice(index, 1);
              updatePhotoPreview();
              
              Swal.fire(
                '¡Eliminada!',
                'La imagen ha sido eliminada correctamente.',
                'success'
              );
            } else {
              Swal.fire(
                'Error',
                'No se pudo eliminar la imagen: ' + response.message,
                'error'
              );
            }
          },
          error: function() {
            Swal.fire(
              'Error',
              'Ocurrió un error al eliminar la imagen',
              'error'
            );
          }
        });
      }
    });
  } else {
    // Si es una foto nueva (aún no guardada), solo eliminar del array
    vueApp.equiposPreAlerta[equipoActivo].fotos.splice(index, 1);
    updatePhotoPreview();
  }
}

  // Función para actualizar la vista previa de las fotos
  function updatePhotoPreview() {
    const preview = document.getElementById("imagePreview")
    const equipoActivo = vueApp.equipoActivo
    preview.innerHTML = ""

    if (vueApp.equiposPreAlerta[equipoActivo] && vueApp.equiposPreAlerta[equipoActivo].fotos) {
      vueApp.equiposPreAlerta[equipoActivo].fotos.forEach((foto, index) => {
        if (foto instanceof File) {
          const reader = new FileReader()
          reader.onload = (e) => {
            addImagePreview(e.target.result, index)
          }
          reader.readAsDataURL(foto)
        } else if (typeof foto === "object" && foto.nombre_foto) {
          // Si es una foto existente del servidor
          addImagePreview(null, index, true, foto.nombre_foto)
        }
      })
    }
  }

  // Actualizar la vista cuando se cambia de equipo
  vueApp.$watch("equipoActivo", (newEquipoIndex) => {
    updatePhotoPreview()
  })

  // Función para cargar fotos iniciales desde la respuesta del servidor
  function cargarFotosIniciales(fotos) {
    if (!Array.isArray(fotos)) {
      console.error("Las fotos no son un array:", fotos)
      return
    }

    console.log("Cargando fotos iniciales:", fotos)

    fotos.forEach((foto) => {
      const equipoIndex = Number.parseInt(foto.equipo_index) || 0

      // Asegurarse de que el equipo tenga un array de fotos
      if (!vueApp.equiposPreAlerta[equipoIndex].fotos) {
        vueApp.equiposPreAlerta[equipoIndex].fotos = []
      }

      // Agregar la foto al array del equipo correspondiente
      vueApp.equiposPreAlerta[equipoIndex].fotos.push({
        id: foto.id,
        nombre_foto: foto.nombre_foto,
        equipo_index: equipoIndex,
      })
    })

    // Actualizar la vista previa si hay un equipo activo
    if (vueApp.equipoActivo !== null) {
      console.log("Actualizando vista previa para equipo:", vueApp.equipoActivo)
      updatePhotoPreview()
    }
  }

  // Exponer la función para que pueda ser llamada desde Vue
  window.cargarFotosIniciales = cargarFotosIniciales
})

