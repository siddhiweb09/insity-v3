@extends('frames.frame')

@section('content')
<style>
    .builder-container {
        display: flex;
        margin: 20px;
        gap: 15px;
        position: relative;
    }

    .canvas-area {
        flex: 1;
        background: #fff;
        border-radius: 8px;
        padding: 15px;
        border: 1px solid #ddd;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease-in-out;
    }

    .tools-sidebar {
        position: fixed;
        top: 0;
        right: 0;
        width: 320px;
        height: 100vh;
        background: #fff;
        border-left: 1px solid #ddd;
        box-shadow: -2px 0 8px rgba(0, 0, 0, 0.05);
        padding: 20px;
        overflow-y: auto;
        transform: translateX(110%);
        transition: transform 0.3s ease-in-out;
        z-index: 1000;
        margin-top: 60px !important;
        height: 90vh !important;
    }

    .tools-sidebar.open {
        transform: translateX(0);
    }

    .toggle-btn {
        position: absolute;
        right: 20px;
        top: 20px;
        z-index: 101;
    }

    #c {
        border: 1px dashed #ccc;
        background: #fff;
        display: block;
        margin: 0 auto;
    }

    .tools-sidebar h4 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .tools-sidebar .form-label {
        font-weight: 500;
        margin-top: 10px;
    }

    .tools-sidebar hr {
        margin: 15px 0;
    }

    .object-prop {
        margin-bottom: 10px;
    }
</style>

<div class="content-wrapper">
    <div class="card overflow-hidden p-4 shadow-none">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="font-weight-500 text-primary mb-0">Build Creatives</h3>
        </div>

        <!-- Toggle Button -->
        <button class="btn btn-secondary toggle-btn" id="toggleSidebarBtn">
            <i class="bi bi-tools"></i> Tools
        </button>

        <!-- Builder Area -->
        <div class="builder-container">
            <!-- Canvas Area -->
            <div class="canvas-area" id="canvasContainer">
                <canvas id="c" width="1000" height="700"></canvas>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="tools-sidebar" id="toolsSidebar">
            <!-- Background upload -->
            <label class="form-label">Upload/select background image</label>
            <input type="file" id="bgUpload" accept="image/*" class="form-control mb-2">
            <button class="btn btn-sm btn-outline-secondary mb-2" id="clear-bg">Clear Background</button>
            <hr>

            <!-- Add text section -->
            <label class="form-label">Add Text</label>
            @php
            $fields = ['name' => 'Name', 'designation' => 'Designation', 'mobile' => 'Mobile', 'email' => 'Email'];
            @endphp

            <select id="presetField" class="form-select mb-2">
                <option value="">-- Choose field (or custom) --</option>
                @foreach($fields as $key => $label)
                <option value="<?php echo '{{' . $key . '}}'; ?>">
                    {{ $label }} (<?php echo '{{' . $key . '}}'; ?>)
                </option>
                @endforeach
                <option value="Custom">Custom text</option>
            </select>
            <input type="text" id="customText" class="form-control mb-2" placeholder="If custom, type text here">
            <button class="btn btn-primary w-100 mb-3" id="addTextBtn">Add Textbox</button>

            <hr>

            <!-- Object Properties -->
            <label class="form-label">Selected Object Properties</label>
            <div id="objectProps" style="display:none;">
                <div class="object-prop">
                    <label>Text</label>
                    <input type="text" id="propText" class="form-control">
                </div>
                <div class="object-prop">
                    <label>Font family</label>
                    <select id="propFont" class="form-select">
                        <option>Arial</option>
                        <option>Helvetica</option>
                        <option>Times New Roman</option>
                        <option>Roboto</option>
                        <option>Open Sans</option>
                    </select>
                </div>
                <div class="d-flex gap-2 mb-2">
                    <input type="number" id="propFontSize" class="form-control" placeholder="Size">
                    <input type="color" id="propColor" class="form-control form-control-color">
                </div>
                <div class="d-flex gap-2 mb-2">
                    <label class="form-check-label me-2">Opacity</label>
                    <input type="range" id="propOpacity" min="0" max="1" step="0.01" value="1" class="form-range">
                </div>
                <div class="d-flex gap-2 mb-2">
                    <button class="btn btn-outline-secondary btn-sm" id="alignLeft">Left</button>
                    <button class="btn btn-outline-secondary btn-sm" id="alignCenter">Center</button>
                    <button class="btn btn-outline-secondary btn-sm" id="alignRight">Right</button>
                </div>
                <div class="d-flex gap-2 mb-2">
                    <button class="btn btn-warning btn-sm" id="bringForward">Bring Forward</button>
                    <button class="btn btn-warning btn-sm" id="sendBack">Send Back</button>
                    <button class="btn btn-danger btn-sm" id="deleteObject">Delete</button>
                </div>
            </div>

            <hr>
            <div class="mb-2">
                <button class="btn rounded-circle mb-1 bg-primary" id="undoBtn">
                    <i class="mdi mdi-undo-variant text-white fs-5"></i>
                </button>
                <button class="btn rounded-circle mb-1 bg-primary" id="redoBtn">
                    <i class="mdi mdi-redo-variant text-white fs-5"></i>
                </button>
                <button class="btn btn-outline-danger mb-1" id="resetBtn">Reset Canvas</button>
            </div>
            <hr>
            <div class="mb-2">
                <input type="text" id="templateTitle" class="form-control mb-2" placeholder="Template title">
                <button class="btn btn-success w-100 mb-2" id="saveTemplateBtn">Save Template</button>
                <button class="btn btn-primary w-100 mb-2" id="previewBtn">Preview</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customJs')
<script>
    $(document).ready(function() {
        const canvas = new fabric.Canvas('c', {
            preserveObjectStacking: true
        });
        canvas.setBackgroundColor('#ffffff', canvas.renderAll.bind(canvas));

        let state = [],
            currentStateIndex = -1;

        function saveState() {
            currentStateIndex++;
            state = state.slice(0, currentStateIndex);
            state.push(JSON.stringify(canvas.toJSON()));
        }
        saveState();

        canvas.on('object:modified', saveState);
        canvas.on('object:added', saveState);

        // Sidebar toggle
        const toggleBtn = document.getElementById('toggleSidebarBtn');
        const sidebar = document.getElementById('toolsSidebar');
        const canvasContainer = document.getElementById('canvasContainer');

        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('open');
        });

        document.addEventListener('click', function(e) {
            // Only close if click is outside sidebar, toggle button AND canvas
            if (
                sidebar.classList.contains('open') &&
                !sidebar.contains(e.target) &&
                e.target !== toggleBtn &&
                !canvasContainer.contains(e.target)
            ) {
                sidebar.classList.remove('open');
            }
        });

        // Background Upload
        $('#bgUpload').on('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(ev) {
                const data = ev.target.result;
                fabric.Image.fromURL(data, function(img) {
                    const canvasAspect = canvas.width / canvas.height;
                    const imgAspect = img.width / img.height;
                    let scaleFactor = imgAspect >= canvasAspect ? canvas.width / img.width : canvas.height / img.height;

                    img.set({
                        selectable: false,
                        evented: false,
                        originX: 'left',
                        originY: 'top'
                    });
                    img.scale(scaleFactor);
                    canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
                    saveState();
                });
            };
            reader.readAsDataURL(file);
        });

        $('#clear-bg').on('click', function() {
            canvas.setBackgroundImage(null, canvas.renderAll.bind(canvas));
            saveState();
        });

        // Add Text
        $('#addTextBtn').on('click', function() {
            const preset = $('#presetField').val();
            const custom = $('#customText').val();
            const textValue = (preset && preset !== 'Custom') ? preset : custom;

            if (!textValue) {
                alert('Please select or enter text!');
                return;
            }

            const text = new fabric.Textbox(textValue, {
                left: 100,
                top: 100,
                width: 300,
                fontSize: 30,
                fill: '#000',
                fontFamily: 'Arial'
            });

            canvas.add(text).setActiveObject(text);
            saveState();
        });

        // Update properties
        function updateProps() {
            const obj = canvas.getActiveObject();
            if (!obj) return;
            $('#objectProps').show();
            $('#propText').val(obj.text || '');
            $('#propFont').val(obj.fontFamily || 'Arial');
            $('#propFontSize').val(obj.fontSize || 30);
            $('#propColor').val(obj.fill || '#000000');
            $('#propOpacity').val(obj.opacity || 1);
        }
        canvas.on('selection:created', updateProps);
        canvas.on('selection:updated', updateProps);
        canvas.on('selection:cleared', () => $('#objectProps').hide());

        $('#propText').on('input', e => {
            const obj = canvas.getActiveObject();
            if (obj) {
                obj.text = e.target.value;
                canvas.renderAll();
                saveState();
            }
        });
        $('#propFont').on('change', e => {
            const obj = canvas.getActiveObject();
            if (obj) {
                obj.fontFamily = e.target.value;
                canvas.renderAll();
                saveState();
            }
        });
        $('#propFontSize').on('input', e => {
            const obj = canvas.getActiveObject();
            if (obj) {
                obj.fontSize = parseInt(e.target.value);
                canvas.renderAll();
                saveState();
            }
        });
        $('#propColor').on('input', e => {
            const obj = canvas.getActiveObject();
            if (obj) {
                obj.set('fill', e.target.value);
                canvas.renderAll();
                saveState();
            }
        });
        $('#propOpacity').on('input', e => {
            const obj = canvas.getActiveObject();
            if (obj) {
                obj.opacity = parseFloat(e.target.value);
                canvas.renderAll();
                saveState();
            }
        });

        $('#alignLeft').on('click', () => {
            const obj = canvas.getActiveObject();
            if (obj) {
                obj.textAlign = 'left';
                canvas.renderAll();
            }
        });
        $('#alignCenter').on('click', () => {
            const obj = canvas.getActiveObject();
            if (obj) {
                obj.textAlign = 'center';
                canvas.renderAll();
            }
        });
        $('#alignRight').on('click', () => {
            const obj = canvas.getActiveObject();
            if (obj) {
                obj.textAlign = 'right';
                canvas.renderAll();
            }
        });

        $('#bringForward').on('click', () => {
            const obj = canvas.getActiveObject();
            if (obj) canvas.bringForward(obj);
        });
        $('#sendBack').on('click', () => {
            const obj = canvas.getActiveObject();
            if (obj) canvas.sendBackwards(obj);
        });
        $('#deleteObject').on('click', () => {
            const obj = canvas.getActiveObject();
            if (obj) {
                canvas.remove(obj);
                saveState();
            }
        });

        $('#undoBtn').on('click', () => {
            if (currentStateIndex > 0) {
                currentStateIndex--;
                canvas.loadFromJSON(state[currentStateIndex], canvas.renderAll.bind(canvas));
            }
        });
        $('#redoBtn').on('click', () => {
            if (currentStateIndex < state.length - 1) {
                currentStateIndex++;
                canvas.loadFromJSON(state[currentStateIndex], canvas.renderAll.bind(canvas));
            }
        });

        $('#resetBtn').on('click', () => {
            canvas.clear();
            canvas.setBackgroundColor('#fff', canvas.renderAll.bind(canvas));
            saveState();
        });

        // Export without blank space
        function exportCanvas() {
            const objects = canvas.getObjects();
            if (!objects.length && !canvas.backgroundImage) return null;

            let minX = Infinity,
                minY = Infinity,
                maxX = 0,
                maxY = 0;
            objects.forEach(obj => {
                const bound = obj.getBoundingRect(true);
                minX = Math.min(minX, bound.left);
                minY = Math.min(minY, bound.top);
                maxX = Math.max(maxX, bound.left + bound.width);
                maxY = Math.max(maxY, bound.top + bound.height);
            });

            if (canvas.backgroundImage) {
                minX = Math.min(minX, 0);
                minY = Math.min(minY, 0);
                maxX = Math.max(maxX, canvas.backgroundImage.width * canvas.backgroundImage.scaleX);
                maxY = Math.max(maxY, canvas.backgroundImage.height * canvas.backgroundImage.scaleY);
            }

            const width = maxX - minX;
            const height = maxY - minY;

            const exportCanvas = new fabric.StaticCanvas(null, {
                width,
                height
            });
            if (canvas.backgroundImage) {
                const bgClone = fabric.util.object.clone(canvas.backgroundImage);
                bgClone.left = -minX;
                bgClone.top = -minY;
                exportCanvas.setBackgroundImage(bgClone, exportCanvas.renderAll.bind(exportCanvas));
            } else {
                exportCanvas.setBackgroundColor('#ffffff', exportCanvas.renderAll.bind(exportCanvas));
            }

            objects.forEach(obj => {
                const clone = fabric.util.object.clone(obj);
                clone.left -= minX;
                clone.top -= minY;
                exportCanvas.add(clone);
            });

            exportCanvas.renderAll();
            return exportCanvas.toDataURL({
                format: 'png'
            });
        }

        $('#previewBtn').on('click', () => {
            const dataURL = exportCanvas();
            if (!dataURL) return alert('Nothing to export!');
            const win = window.open();
            win.document.write('<img src="' + dataURL + '" style="max-width:100%;"/>');
        });

        $('#saveTemplateBtn').on('click', function() {
            const title = $('#templateTitle').val().trim();
            if (!title) {
                alert('Enter template title');
                return;
            }

            canvas.discardActiveObject();
            canvas.renderAll();

            const rawJson = canvas.toJSON(['selectable', 'id']);
            const exportedImage = JSON.stringify(rawJson.backgroundImage);
            delete rawJson.backgroundImage; // remove background image
            delete rawJson.overlayImage; // if overlay image exists

            const json = JSON.stringify(rawJson);

            const payload = {
                title: title,
                json: json,
                exportedImage: exportedImage
            };

            $.ajax({
                url: "{{ route('store.creativeTemplate') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: payload,
                success: function(res) {
                    if (res.success) {
                        alert('Template saved successfully');
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        alert('Save failed');
                        console.log(res);
                    }
                }
            });
        });
    });
</script>
@endsection