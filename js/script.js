'use strict';

var expandControls = document.querySelectorAll('.expand-control');

var hidePopups = function() {
  [].forEach.call(document.querySelectorAll('.expand-list'), function(item) {
    item.classList.add('hidden');
  });
};

document.body.addEventListener('click', hidePopups, true);

[].forEach.call(expandControls, function(item) {
  item.addEventListener('click', function() {
    item.nextElementSibling.classList.toggle('hidden');
  });
});

var $checkbox = document.getElementById('show-complete-tasks');

if ($checkbox) {
  $checkbox.addEventListener('change', function(event) {
    var is_checked = +event.target.checked;

    window.location = '/index.php?show_completed=' + is_checked;
  });
}

var tasks = document.querySelector('.tasks');
if (tasks) {
  var checkboxes = tasks.querySelectorAll('.checkbox__input');

  [].forEach.call(checkboxes, function(item) {
    item.addEventListener('change', function(event) {
      var isComplete = +item.checked;
      var taskId = item.getAttribute('data-task');

      window.location = '/index.php?task=' + taskId + '&complete=' + isComplete;
    });
  });
}

var btnModalClose = document.querySelector('.modal__close');
if (btnModalClose) {
  btnModalClose.addEventListener('click', function(event) {
    window.location = '/index.php';
  });
}
