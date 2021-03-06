<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <link rel="stylesheet"
          href="{{ mix('/css/vendor.css') }}">
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
          crossorigin="anonymous">
    <meta name="csrf-token"
          content="{{ csrf_token() }}">
    <script>window.Laravel = { csrfToken: "{{ csrf_token() }}" };  </script>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/css/tempusdominus-bootstrap-4.min.css"/>
    <script src="{{ mix('/js/app.js') }}"></script>
    <script src="{{ mix('/js/all.js') }}"></script>


    <title>Document</title>
    <style>
        .mainColor {
            background-color: #ffc5cb;
        }

        #temperature {
            font-size: 35px;
            height: 70px;
        }

        .main {
            position: relative;
        }

        .update_popup {
            position: absolute;
            top: 15px;
            left: 125px;
            display: none;
        }
    </style>

</head>
<body>

{{--Navbar--}}
<nav class="navbar navbar-expand-lg navbar-light mainColor">
    <a class="navbar-brand"
       href="#">體溫紀錄小幫手</a>
    <button class="navbar-toggler"
            type="button"
            data-toggle="collapse"
            data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse"
         id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link"
                   href="#">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   data-toggle="modal"
                   data-target="#dateRangePicker"
                   href="#">產生紀錄表</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">


    {{--今天日期--}}
    <div class="card mt-3">

        <div class="card-header mainColor">

            <div class="form-group">
                <div class="input-group date"
                     id="datetimepicker1"
                     data-target-input="nearest">
                    <input type="text"
                           id="datetimepickerInput"
                           value="{{ $today }}"
                           class="form-control form-control-lg datetimepicker-input"
                           data-target="#datetimepicker1"/>
                    <div class="input-group-append"
                         data-target="#datetimepicker1"
                         data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body main">
            {{--體溫輸入--}}
            <h5 class="card-title">今日體溫</h5>
            <div class="update_popup mainColor text-light rounded p-1">紀錄以更新</div>
            <div class="input-group mt-3">
                <input type="number"
                       class="form-control form-control-lg"
                       id="temperature"
                       value=""
                       min="36"
                       max="37.5"
                       aria-label="°C (with dot and two decimal places)">
                <div class="input-group-append">
                    <span class="input-group-text mainColor"
                          style="font-size:32px;">°C</span>
                </div>
            </div>
        </div>

        <input type="hidden"
               id="user_id"
               value="{{$user_id}}">

        <div class="card-footer mainColor">
            <input type="checkbox"
                   class="js-switch"
                   id="is_period"
            />
            <span style="padding-left: 5px; font-size: 18px;">姨媽來了沒？</span>
        </div>

    </div>


    <!-- Modal -->
    <div class="modal fade"
         id="dateRangePicker"
         tabindex="-1"
         role="dialog"
         aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog"
             role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">請選擇時間區段</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="input-group"><input type="text" id="start" class="form-control form-control-lg">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="generateImage" class="btn btn-primary">產生檔案</button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Scripts -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
        crossorigin="anonymous"></script>
<script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/js/tempusdominus-bootstrap-4.min.js"></script>

<script>

  var elems     = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
  var switchery = null;
  elems.forEach(function(html) {
    switchery = new Switchery(html, { size: 'large' });
  });


  var beginDate, endDate;

  $(function() {
    $('#datetimepicker1').datetimepicker({
      format: 'YYYY-MM-DD',
      locale: 'zh-tw',
    });
  });

  function setSwitchery(switchElement, checkedBool) {
    if ((checkedBool && !switchElement.isChecked()) || (!checkedBool && switchElement.isChecked())) {
      switchElement.setPosition(true);
      switchElement.handleOnchange(true);
    }
  }


  $("#datetimepicker1").on("change.datetimepicker", function(e) {

    axios.post('/api/v1/body_temperature/query', {
      date: $("#datetimepickerInput").val(),
      user_id: $("#user_id").val()
    }).then(function(res) {
      let { data } = res.data;

      if (data.temperature === 0) {
        $("#temperature").val('');
      }else {
        $("#temperature").val(data.temperature);
      }
      setSwitchery(switchery, data.is_period);
    });
  });


  $('.js-switch').on('change', function() {
    update();
  });

  function parseInputToFloat() {
    let val    = parseFloat($("#temperature").val()) + "";
    let length = val.length;

    if (length > 2) {
      val = val.substring(0, 2) + "." + val.substring(2, length)
    } else {
      val = val.substring(0, 2);
    }

    let parseVal = parseFloat(val);
    $("#temperature").val(parseVal);
  };


  $("#temperature").on('change', function() {

    if ($('#temperature').val().indexOf('.') === -1) {
      parseInputToFloat();
    }

    if ($('#temperature').val() < 36) {
      swal({
        title: "溫度太低了！！",
        text: "你丟搞喔！",
        icon: "warning",
        button: "OK",
      });
      return false;
    }
    if ($('#temperature').val() >= 37.5) {
      swal({
        title: "溫度太高了！！",
        text: "你發騷喔！??",
        icon: "warning",
        button: "OK",
      });
      return false;
    }
    update();
  });


  function update() {
    axios.post('/api/v1/body_temperature/update', {
      date: $("#datetimepickerInput").val(),
      body_temperature: $("#temperature").val(),
      is_period: $('.js-switch').is(":checked"),
      user_id: $("#user_id").val()
    }).then(function() {

      $(".update_popup").fadeIn();
      setTimeout(function() {
        $(".update_popup").fadeOut();
      }, 1500)
    });
  }


  $('#generateImage').on('click', function() {
    swal({
      title: `確定要產生 ${beginDate} 到 ${endDate}區間的檔案嗎？`,
      icon: 'warning',
      buttons: true,
      dangerMode: true,
    }).then((ok) => {
      if (ok) {
        generateImage();
        $('#dateRangePicker').modal('hide');
      }
    });

  });

  function generateImage() {

    let data = {
      begin: beginDate,
      end: endDate,
      user_id: $("#user_id").val()
    };

    axios({
      method: 'post',
      url: '/api/v1/body_temperature/generateImage',
      data: JSON.stringify(data),
      headers: {
        'Content-Type': 'application/json',
      },

    }).then(function(res) {

      let { url } = res.data.data;

      swal({
        title: `你的${beginDate} 到 ${endDate} 體溫紀錄表已經完成囉！`,
        text: `連結將在3分鐘內刪除`,
        icon: "success",
        button: "查看紀錄表",
      }).then((ok) => {
        if (ok) {

          let urlSplit = url.split('/');
          let filename = urlSplit[urlSplit.length - 1];
          setTimeout(() => {
            axios.post('/api/v1/body_temperature/delete', { filename });
          }, 180000);

          window.open(url, '_blank');
        }
      });
    }).catch(err => {
      let { status } = err.response;
      if (status === 404) {
        swal({
          title: `你的 ${beginDate} 到 ${endDate} 區間的資料不足無法產生紀錄表喔！`,
          text: "",
          icon: "error",
          button: "OK",
        });
      }
    })
  }


  var picker = new Lightpick({
    field: document.getElementById('start'),
    singleDate: false,
    numberOfMonths: 3,
    numberOfColumns: 1,
    maxDays: 50,
    selectForward: false,
    orientation: 'bottom',
    format: 'YYYY/MM/DD',
    onSelect: function(start, end) {
      if (!start || !end) {
        return false;
      }

      beginDate = start.format("YYYY/MM/DD");
      endDate   = end.format("YYYY/MM/DD");

      let diffInDays = end.diff(start, 'day');
      console.log(start.format("YYYY/MM/DD"), end.format("YYYY/MM/DD"), diffInDays);
    },

  });


</script>
</body>
</html>




