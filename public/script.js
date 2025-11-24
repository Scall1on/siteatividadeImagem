// Funções para a página de upload
if (document.getElementById('uploadForm')) {
    const uploadArea = document.getElementById('uploadArea');
    const uploadText = document.getElementById('uploadText');
    const imageInput = document.getElementById('imageInput');
    const preview = document.getElementById('preview');
    const uploadForm = document.getElementById('uploadForm');
    const submitBtn = document.getElementById('submitBtn');
    const changeImageBtn = document.getElementById('changeImageBtn');
    const renameSection = document.getElementById('renameSection');
    const customNameRadio = document.getElementById('customName');
    const customNameInput = document.getElementById('customNameInput');
    const customFileNameInput = document.getElementById('customFileNameInput');
    const uploadProgress = document.getElementById('uploadProgress');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    
    // Controla a exibição do campo de nome personalizado
    document.querySelectorAll('input[name="nameOption"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'custom') {
                customNameInput.style.display = 'block';
                customFileNameInput.focus();
            } else {
                customNameInput.style.display = 'none';
            }
        });
    });

    // Clique na área de upload abre o seletor
    uploadArea.addEventListener('click', function() {
        imageInput.click();
    });

    // Preview da imagem selecionada
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            showPreview(file);
        }
    });

    // Drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            imageInput.files = e.dataTransfer.files;
            showPreview(file);
        }
    });
    
    // Botão para escolher outra imagem
    changeImageBtn.addEventListener('click', function() {
        imageInput.click();
    });

    // Mostrar preview da imagem
    function showPreview(file) {
        // Validar tamanho do arquivo
        const maxSize = 10 * 1024 * 1024; // 10MB
        const isTooBig = file.size > maxSize;
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            // Esconde o texto de upload e mostra a imagem no lugar
            uploadText.style.display = 'none';
            uploadArea.style.border = 'none';
            uploadArea.style.padding = '0';
            uploadArea.style.cursor = 'default';
            uploadArea.style.background = 'transparent';
            
            // Cria preview da imagem na área de upload
            uploadArea.innerHTML = `
                <img src="${e.target.result}" alt="Preview" style="width: 100%; height: 100%; object-fit: contain; border-radius: 8px;">
            `;
            
            // Mostra informações no preview
            const info = document.createElement('div');
            info.className = 'preview-info';
            
            // Adiciona aviso se o arquivo for muito grande
            let sizeWarning = '';
            if (isTooBig) {
                sizeWarning = '<p style="color: #e74c3c; font-weight: bold;">⚠️ Este arquivo excede o tamanho máximo de 10MB!</p>';
            }
            
            info.innerHTML = `
                <p><strong>Nome:</strong> ${file.name}</p>
                <p><strong>Tamanho:</strong> ${formatFileSize(file.size)}</p>
                ${sizeWarning}
            `;
            
            preview.innerHTML = '';
            preview.appendChild(info);
            
            // Mostra a opção de renomear e o botão de trocar imagem
            if (!isTooBig) {
                renameSection.style.display = 'block';
                changeImageBtn.style.display = 'inline-block';
            }
            
            // Desabilita o botão de enviar se for muito grande
            if (isTooBig) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Arquivo muito grande';
                submitBtn.style.opacity = '0.5';
                changeImageBtn.style.display = 'inline-block';
            } else {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar Imagem';
                submitBtn.style.opacity = '1';
            }
        };
        
        reader.readAsDataURL(file);
    }

    // Submit do formulário com AJAX
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validar tamanho do arquivo ANTES de enviar
        const file = imageInput.files[0];
        if (!file) {
            showError('Por favor, selecione uma imagem.');
            return;
        }
        
        const maxSize = 10 * 1024 * 1024; // 10MB em bytes
        if (file.size > maxSize) {
            showError('O arquivo excede o tamanho máximo de 10MB. Tamanho atual: ' + formatFileSize(file.size));
            return;
        }
        
        // Validar nome personalizado se selecionado
        const nameOption = document.querySelector('input[name="nameOption"]:checked').value;
        if (nameOption === 'custom') {
            const customName = customFileNameInput.value.trim();
            if (!customName) {
                showError('Por favor, digite um nome para o arquivo.');
                customFileNameInput.focus();
                return;
            }
        }
        
        const formData = new FormData(uploadForm);
        
        // Desabilita o botão
        submitBtn.disabled = true;
        submitBtn.textContent = 'Enviando...';
        
        // Mostra a barra de progresso
        uploadProgress.style.display = 'block';
        
        // Cria a requisição AJAX
        const xhr = new XMLHttpRequest();
        
        // Monitora o progresso do upload
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressFill.style.width = percentComplete + '%';
                progressText.textContent = `Enviando... ${Math.round(percentComplete)}%`;
            }
        });
        
        // Quando o upload completar
        xhr.addEventListener('load', function() {
            if (xhr.status === 200) {
                progressText.textContent = 'Upload concluído!';
                progressFill.style.width = '100%';
                
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 1000);
            } else {
                const response = JSON.parse(xhr.responseText);
                const errorMsg = response.errors ? response.errors.join(', ') : response.error;
                showError(errorMsg);
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar Imagem';
                uploadProgress.style.display = 'none';
            }
        });
        
        // Trata erros de rede
        xhr.addEventListener('error', function() {
            showError('Erro de rede ao enviar o arquivo');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Enviar Imagem';
            uploadProgress.style.display = 'none';
        });
        
        xhr.open('POST', 'upload_process.php');
        xhr.send(formData);
    });
    
    // Função para mostrar erro na página
    function showError(message) {
        // Remove mensagens de erro anteriores
        const oldError = document.querySelector('.error-message');
        if (oldError) {
            oldError.remove();
        }
        
        // Cria nova mensagem de erro
        const errorDiv = document.createElement('div');
        errorDiv.className = 'message error error-message';
        errorDiv.textContent = message;
        errorDiv.style.marginTop = '20px';
        
        // Insere antes do preview
        preview.parentNode.insertBefore(errorDiv, preview);
        
        // Auto-remove após 10 segundos
        setTimeout(function() {
            errorDiv.style.transition = 'opacity 0.5s ease';
            errorDiv.style.opacity = '0';
            setTimeout(function() {
                errorDiv.remove();
            }, 500);
        }, 10000);
    }
}

// Funções para a página inicial (galeria)
function viewImage(filename) {
    fetch(`get_image.php?filename=${encodeURIComponent(filename)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showImageModal(data.image);
            } else {
                showErrorMessage('Erro ao carregar informações da imagem');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showErrorMessage('Erro ao carregar informações da imagem');
        });
}

function showImageModal(image) {
    const modal = document.getElementById('imageModal');
    const modalBody = document.getElementById('modalBody');
    
    modalBody.innerHTML = `
        <img src="${image.url}" alt="${image.filename}" class="modal-image">
        <div class="image-details">
            <div class="detail-row">
                <span class="detail-label">Nome do Arquivo:</span>
                <span class="detail-value">${image.filename}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Data de Upload:</span>
                <span class="detail-value">${formatDate(image.upload_date)}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tamanho:</span>
                <span class="detail-value">${formatFileSize(image.size)}</span>
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-danger" onclick="deleteImage('${image.filename}')">
                Excluir Imagem
            </button>
            <button class="btn btn-secondary" onclick="closeModal()">
                Fechar
            </button>
        </div>
    `;
    
    modal.classList.add('active');
}

function closeModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.remove('active');
}

function deleteImage(filename) {
    // Mostra modal de confirmação customizado
    showConfirmModal(
        'Confirmar Exclusão',
        'Tem certeza que deseja excluir esta imagem? Esta ação não pode ser desfeita.',
        function() {
            // Callback se confirmar
            executeDelete(filename);
        }
    );
}

function executeDelete(filename) {
    fetch('delete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `filename=${encodeURIComponent(filename)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            window.location.reload();
        } else {
            showErrorMessage('Erro ao excluir imagem: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showErrorMessage('Erro ao excluir imagem');
    });
}

function showConfirmModal(title, message, onConfirm) {
    // Remove modal de confirmação anterior se existir
    const oldConfirm = document.getElementById('confirmModal');
    if (oldConfirm) {
        oldConfirm.remove();
    }
    
    // Cria o modal de confirmação
    const confirmModal = document.createElement('div');
    confirmModal.id = 'confirmModal';
    confirmModal.className = 'modal active';
    confirmModal.style.zIndex = '2000'; // Maior que o modal de imagem
    
    confirmModal.innerHTML = `
        <div class="modal-content" style="max-width: 400px;">
            <h2 style="margin-bottom: 20px; color: var(--dark-bg);">${title}</h2>
            <p style="margin-bottom: 30px; font-size: 1.1rem; line-height: 1.6;">${message}</p>
            <div style="display: flex; gap: 15px; justify-content: center;">
                <button class="btn btn-danger" id="confirmYes">Sim, Excluir</button>
                <button class="btn btn-secondary" id="confirmNo">Cancelar</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(confirmModal);
    
    // Event listeners
    document.getElementById('confirmYes').addEventListener('click', function() {
        confirmModal.remove();
        if (onConfirm) onConfirm();
    });
    
    document.getElementById('confirmNo').addEventListener('click', function() {
        confirmModal.remove();
    });
    
    // Fechar ao clicar fora
    confirmModal.addEventListener('click', function(e) {
        if (e.target === confirmModal) {
            confirmModal.remove();
        }
    });
}

function showErrorMessage(message) {
    // Remove mensagens anteriores
    const oldError = document.querySelector('.floating-error');
    if (oldError) {
        oldError.remove();
    }
    
    // Cria mensagem de erro flutuante
    const errorDiv = document.createElement('div');
    errorDiv.className = 'message error floating-error';
    errorDiv.textContent = message;
    errorDiv.style.position = 'fixed';
    errorDiv.style.top = '20px';
    errorDiv.style.left = '50%';
    errorDiv.style.transform = 'translateX(-50%)';
    errorDiv.style.zIndex = '9999';
    errorDiv.style.minWidth = '300px';
    errorDiv.style.textAlign = 'center';
    
    document.body.appendChild(errorDiv);
    
    // Auto-remove após 5 segundos
    setTimeout(function() {
        errorDiv.style.transition = 'opacity 0.5s ease';
        errorDiv.style.opacity = '0';
        setTimeout(function() {
            errorDiv.remove();
        }, 500);
    }, 5000);
}

// Fecha o modal ao clicar fora dele
window.onclick = function(event) {
    const modal = document.getElementById('imageModal');
    if (event.target === modal) {
        closeModal();
    }
}

// Função auxiliar para formatar tamanho de arquivo
function formatFileSize(bytes) {
    if (bytes >= 1000000) {
        return (bytes / 1000000).toFixed(2) + ' MB';
    } else if (bytes >= 1000) {
        return (bytes / 1000).toFixed(2) + ' KB';
    }
    return bytes + ' bytes';
}

// Função auxiliar para formatar data
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Auto-hide mensagens após 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const messages = document.querySelectorAll('.message');
    messages.forEach(function(message) {
        setTimeout(function() {
            message.style.transition = 'opacity 0.5s ease';
            message.style.opacity = '0';
            setTimeout(function() {
                message.remove();
            }, 500);
        }, 5000);
    });
});
