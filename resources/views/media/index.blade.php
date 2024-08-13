<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Media Task</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body>
    <div class="container p-2">
        <h1 class="text-center">Media Library</h1>
        <div id="media-library" class="form-wrapper mt-4">
            <div class="media-library-alert-container my-2"></div>
            <form class="media-library-form" action="javascript:void(0)" method="post">
                <div class="form-group">
                    <input class="media-library-file-input form-control" type="file">
                </div>
                <div class="form-group mt-3 text-center">
                    <button type="submit" class="media-library-submit btn" data-state="idle"></button>
                </div>

                <div class="media-library-progress mt-3">
                    <div class="progress">
                        <div class="media-library-progress-bar progress-bar" role="progressbar"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
