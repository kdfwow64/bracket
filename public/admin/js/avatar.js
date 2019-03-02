jQuery.event.props.push('dataTransfer');

// IIFE to prevent globals
(function () {
    var s;

    function avatarObject(element) {
        var ele = element
        this.settings = {
                fileInput: element
            },

            this.init = function () {
                s = this.settings;
                this.bindUIActions();
                return ele
            },

            this.bindUIActions = function () {
                var list = this;
                var timer;

                $(document).delegate(s.fileInput, 'change', function (event) {
                    event.preventDefault();
                    list.handleDrop(event.target.files);
                });
            },

            this.showDroppableArea = function () {
                s.bod.addClass("droppable");
            },

            this.hideDroppableArea = function () {
                s.bod.removeClass("droppable");
            },

            this.handleDrop = function (files) {
                var list = this;
                // Multiple files can be dropped. Lets only deal with the "first" one.
                var file = files[0];
                if (file.type.match('image.*')) {

                    list.resizeImage(file, function (data) {
                        list.placeImage(file, data, ele);
                    });

                } else {

                    //alert("That file wasn't an image.");

                }

            },

            this.resizeImage = function (file, callback) {
                var list = this;
                var fileTracker = new FileReader;
                fileTracker.onload = function (event) {
                    var data = event.target.result;
                    list.placeImage(file, data, ele);
                }
                fileTracker.readAsDataURL(file);

                fileTracker.onabort = function () {
                    // alert("The upload was aborted.");
                }
                fileTracker.onerror = function () {
                    //alert("An error occured while reading the file.");
                }
                fileTracker.onprogress = function (data) {

                    if (data.lengthComputable) {
                        $("#image-progress").show()
                        var progress = parseInt(((data.loaded / data.total) * 100), 10);
                        console.log(progress);
                        $("#image-progress > .progress-bar").css("width", progress + "%");
                    }
                }


            },
            this.placeImage = function (file, data, ele) {
                $(ele).parent().parent().find("img").attr("src", data);

            }
    }
    var packageInfo = new avatarObject("#profile-image");
    packageInfo.placeImage = function (file, data, ele) {
        $("#dataImage").attr("src", data)
    }
    packageInfo.init();
})();