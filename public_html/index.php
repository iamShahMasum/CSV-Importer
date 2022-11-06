<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Import</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
            integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

    <style>
        .loader {
            width: 500px;
            margin: 0 auto;
            border-radius: 10px;
            border: 4px solid transparent;
            position: relative;
            padding: 1px;
        }

        .loader:before {
            content: '';
            border: 1px solid blue;
            border-radius: 10px;
            position: absolute;
            top: -4px;
            right: -4px;
            bottom: -4px;
            left: -4px;
        }

        .loader .loaderBar {
            position: absolute;
            border-radius: 10px;
            top: 0;
            right: 100%;
            bottom: 0;
            left: 0;
            background: blue;
            width: 0;
            animation: borealisBar 2s linear infinite;
        }

        @keyframes borealisBar {
            0% {
                left: 0%;
                right: 100%;
                width: 0%;
            }
            10% {
                left: 0%;
                right: 75%;
                width: 25%;
            }
            90% {
                right: 0%;
                left: 75%;
                width: 25%;
            }
            100% {
                left: 100%;
                right: 0%;
                width: 0%;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 pt-1 mt-2" style="display: flex; justify-content: center; align-items: center;height: 95vh;">
            <div class="card">
                <div class="card-header">ICLOUD EMS</div>
                <div class="card-body shadow-sm">
                    <form class="mb-0" id="import-form">

                        <div class="row">
                            <div class="col-6">
                                <label class="">Start From</label>
                                <input class="form-control" id="start_row" type="number">
                            </div>

                            <div class="col-6">
                                <label class="" for="">End To</label>
                                <input class="form-control" id="end_row" type="number">
                            </div>

                        </div>

                        <div class="input-group border-top  border-bottom p-3 mb-3 mt-2">
                            Excel File : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input required type="file" class="pl-2" name="csv_file"
                                                                              id="csv_file">
                        </div>
                        <div class="loader mb-3 d-none">
                            <div class="loaderBar"></div>
                        </div>
                        <p id="status"></p>

                        <div class="pt-1" style="display: flex; justify-content: end;">
                            <button id="button" type="submit" class="btn btn-primary btn-md">Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     *
     * @type {jQuery|HTMLElement}
     */
    const $file = $("#csv_file");
    const $importForm = $("#import-form");
    const $startRow = $("#start_row");
    const $endRow = $("#end_row");

    const _handleImport = async (e) => {
        e.preventDefault();

        $("#status").html("<span>Pushing data...</span>");
        $(".loader").removeClass("d-none").addClass("d-block");

        $(':button[type="submit"]').prop('disabled', true);

        let fd = new FormData();
        fd.append('csvfile', $file[0].files[0], $file[0].files[0].name);
        fd.append('startFrom', $startRow.val());
        fd.append('endTo', $endRow.val());

        console.log("request", $file[0].files[0])
        const response = await (await fetch('api/csvimport', {body: fd, method: "POST"})).json();

        console.log("response", response);

        if (response) {
            $(".loader")
                .removeClass("d-block")
                .addClass("d-none")

            $("#status").html("<span> ✓ Data pushed </span>");

            $(':button[type="submit"]').prop('disabled', false);
        }
    }

    $(document).ready(() => {
        $importForm.on("submit", _handleImport);
    })

</script>

</body>
</html>