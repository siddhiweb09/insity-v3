@extends('frames.frame')

@section('content')
    <div class="content-wrapper">
        <div class="card overflow-hidden p-4 shadow-none">
            <h3 class="font-weight-500 mb-xl-4 text-primary">Generate & Download Creative Image</h3>
        </div>
        <div style="overflow:auto;">
            <!-- Canvas will auto-resize -->
            <canvas id="previewCanvas" data-template-id="{{ $templateId }}"></canvas>
        </div>
        <div class="mt-3 d-flex gap-2">
            <button id="generateBtn" class="btn btn-primary">Generate & Download</button>
            <button id="changeBgBtn" class="btn btn-secondary">Change Background</button>
            <input type="file" id="bgFileInput" accept="image/*" style="display:none;" />
        </div>

    </div>
@endsection

@section('customJs')
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            });

            const authUser = {!! json_encode([
        'name' => $authUser->employee_name ?? '',
        'designation' => $authUser->job_title_designation ?? '',
        'email' => $authUser->email_id_official ?? '',
        'employee_code' => $authUser->employee_code ?? '',
        'mobile' => $authUser->mobile_no_official ?? '',
        'zone' => $authUser->zone ?? '',
        'branch' => $authUser->branch ?? ''
    ]) !!};

            const canvas = new fabric.StaticCanvas('previewCanvas');
            const templateId = $('#previewCanvas').data('template-id');

            if (!templateId || templateId <= 0) {
                alert('No template selected.');
                return;
            }

            let originalWidth = 0;
            let originalHeight = 0;

            $.ajax({
                url: '{{ route("loadCreativeTemplates") }}',
                type: 'GET',
                data: { id: templateId },
                dataType: 'json',
                success: function (res) {
                    if (!res.success) {
                        alert('Failed to load template: ' + (res.error || 'Unknown error'));
                        return;
                    }

                    const template = res.template;

                    const loadCanvasObjects = () => {
                        if (template.image_json) {
                            canvas.loadFromJSON(template.image_json, function () {
                                parsedObjects = JSON.parse(template.image_json).objects;
                                canvas.renderAll();
                            });
                        }
                    };

                    const setCanvasBackground = (imageUrl) => {
                        fabric.Image.fromURL(imageUrl + '?v=' + Date.now(), function (img) {
                            originalWidth = img.width;
                            originalHeight = img.height;

                            canvas.setWidth(originalWidth);
                            canvas.setHeight(originalHeight);

                            canvas.setBackgroundImage(img, function () {
                                canvas.renderAll();
                            }, {
                                originX: 'left',
                                originY: 'top',
                                crossOrigin: 'anonymous'
                            });
                        }, { crossOrigin: 'anonymous' });
                    };

                    if (template.bg_image) {
                        let imageUrl = template.bg_image.replace('uploads', 'assets/images/creative_images');
                        setCanvasBackground(imageUrl);
                        loadCanvasObjects();
                    } else {
                        loadCanvasObjects();
                    }

                    // ✅ Change Background Button
                    $('#changeBgBtn').on('click', function () {
                        $('#bgFileInput').click();
                    });

                    $('#bgFileInput').on('change', function (e) {
                        const file = e.target.files[0];
                        if (!file) return;

                        const formData = new FormData();
                        formData.append('bg_image', file);
                        formData.append('template_id', templateId);

                        $.ajax({
                            url: '{{ route("updateCreativeBackground") }}',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function (res) {
                                if (res.success && res.image_url) {
                                    // ✅ Reload background from server with cache-busting
                                    fabric.Image.fromURL(res.image_url + '?v=' + Date.now(), function (img) {
                                        // ✅ Keep existing canvas size
                                        img.scaleToWidth(canvas.getWidth());
                                        img.scaleToHeight(canvas.getHeight());

                                        canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
                                            originX: 'left',
                                            originY: 'top'
                                        });

                                        // ✅ Update template.bg_image
                                        template.bg_image = res.image_url;
                                    }, { crossOrigin: 'anonymous' });
                                } else {
                                    alert('Error: ' + res.message);
                                }
                            },
                            error: function () {
                                alert('Failed to update background.');
                            }
                        });
                    });

                    // ✅ Generate & Download Button
                    // ✅ Generate & Download Button
                    $('#generateBtn').off('click').on('click', function () {
                        // ✅ Replace placeholders dynamically
                        canvas.getObjects().forEach(obj => {
                            if ((obj.type === 'textbox' || obj.type === 'text') && obj.text) {
                                obj.set('text', obj.text.replace(/\{\{(\w+)\}\}/g, (_, key) => authUser[key] ?? ''));
                            }
                        });

                        canvas.renderAll();

                        // ✅ Generate PNG from current canvas (with background)
                        const dataURL = canvas.toDataURL({ format: 'png' });

                        // ✅ Download the image
                        const a = document.createElement('a');
                        a.href = dataURL;
                        a.download = 'creative_' + Date.now() + '.png';
                        a.click();
                    });

                }
            });
        });
    </script>
@endsection