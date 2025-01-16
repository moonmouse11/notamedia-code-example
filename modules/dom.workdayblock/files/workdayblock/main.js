$(document).ready(() => {

  // dayOpened
  // dayPaused
  // dayClosed
  // dayNotStarted
  // dayExpired

  var usr

  var workdayStatus = ''
  var blockerTemplate = $(`
    <div id="workday-blocker" style="display: none; position: fixed;z-index: 999; inset: 0; background: rgba(0, 0, 0, 0.8); color: white; text-align: center; justify-content: center; align-items: center">
        <div>
            <h1 id="start-workday-h" style="font-size: 40px;margin-bottom: 30px;color: white">Пожалуйста, начните рабочий день</h1>
            <button id="start-workday-btn" class="ui-btn ui-btn-primary ui-btn-lg">Начать рабочий день</button>
        </div>
    </div>
  `)

  function setWorkDayBlockerStyleDisplay(value) {
    $('#workday-blocker').css({display: value});
  }

  function restCallMethod(method, callback) {
    return new Promise((resolve) => BX.rest.callMethod(method, {}, res => resolve(callback(res))))
  }

  function sleep(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
  }

  function checkStatus() {
    return restCallMethod('timeman.status', res => res.answer.result.STATUS)
  }

  function setBlockerText({title_text, btn_text}) {
    $('#start-workday-h').html(title_text)
    $('#start-workday-btn').html(btn_text)
  }

  // Функция для проверки статуса рабочего дня
  async function checkWorkdayStatus() {
    if (window.location.href.includes('/bitrix/')) return
    workdayStatus = await checkStatus()
    if (usr?.need_block && workdayStatus !== 'OPENED') {
      // Если рабочий день не начат, показать блокировщик
      setWorkDayBlockerStyleDisplay('flex');
    }
    if (usr?.need_block && workdayStatus === 'OPENED') {
      setWorkDayBlockerStyleDisplay('none');
    }

    if (usr?.need_block && workdayStatus === 'EXPIRED') {
      $('#start-workday-h').html('Вы не закрыли предыдущий рабочий день')
      $('#start-workday-btn').html('Открыть панель для завершения').removeClass('ui-btn-primary').addClass('ui-btn-danger')
    }
  }

  // Функция для начала рабочего дня
  function startWorkday() {
    restCallMethod('timeman.open', res => {
      if (res.answer.result.ACTIVE) {
        $(".timeman-block").trigger("click")
        $(".timeman-block").trigger("click")
        setWorkDayBlockerStyleDisplay('none');
      }
    })
  }

  function expireWorkDay() {
    $(".timeman-block").trigger("click")
  }

  // Функция для проверки принадлежности к отделу или списку пользователей
  async function checkUserDepartmentAndStatus() {
    var user = (await (await BX.ajax.runAction('dom:workdayblock.api.user.data', {method: 'GET'}))).data;
    usr = user

    console.log(usr)
    usr.need_block && console.log('Данный пользователь добавлен в список контроля старта рабочего дня!')
    
    user?.need_block && await checkWorkdayStatus();
  }

  function handleWorkday() {
    if (usr?.need_block && workdayStatus === 'EXPIRED') {
      expireWorkDay()
    } else if (usr?.need_block && workdayStatus !== 'OPENED') {
      startWorkday()
    }
  }

  $(document.body).append(blockerTemplate);

  checkUserDepartmentAndStatus();

  document.addEventListener('click', async (e) => {
    if (e.target.closest('#timeman_main .ui-btn.ui-btn-danger.ui-btn-icon-stop')) {
      //Завершить рабочий день
      if (usr?.need_block && workdayStatus !== 'EXPIRED') {
        $(".timeman-block").trigger("click")
        setBlockerText({
          title_text: 'Пожалуйста, начните рабочий день',
          btn_text: 'Начать рабочий день'
        })
      }
    }
    if (e.target.closest('#timeman_main .ui-btn.ui-btn-icon-pause.tm-btn-pause')) {
      //Перерыв
      $(".timeman-block").trigger("click")
      setBlockerText({
        title_text: 'Пожалуйста, продолжите рабочий день',
        btn_text: 'Продолжить рабочий день'
      })
    }
    if (e.target.closest('#timeman_main .ui-btn.ui-btn-success.ui-btn-icon-start')) {
      //Продолжить рабочий день
      $(".timeman-block").trigger("click")
      setBlockerText({
        title_text: 'Пожалуйста, продолжите рабочий день',
        btn_text: 'Продолжить рабочий день'
      })
    }
    if (e.target.closest('#timeman_main .ui-btn.ui-btn-icon-start.tm-btn-start')) {
      //Продолжить
      $(".timeman-block").trigger("click")
      setBlockerText({
        title_text: 'Пожалуйста, продолжите рабочий день',
        btn_text: 'Продолжить рабочий день'
      })
    }
    if (e.target.closest('.popup-window-button.popup-window-button-decline')) {
      //Незавершенный день
      if (!$('.bx-tm-popup-clock-wnd-report textarea').val()) return

      $(".timeman-block").trigger("click")
      setBlockerText({
        title_text: 'Пожалуйста, начните рабочий день',
        btn_text: 'Начать рабочий день'
      })
      $('#start-workday-btn').removeClass('ui-btn-danger').addClass('ui-btn-primary')
    }
    await sleep(200)
    usr?.need_block && await checkWorkdayStatus()
  }, true)


  $('#start-workday-btn').on('click', handleWorkday);

})