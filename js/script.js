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

var $checkbox = document.getElementsByClassName('checkbox__input')[0];

$checkbox.addEventListener('change', function(event) {
  var is_checked = +event.target.checked;

  window.location = '/index.php?show_completed=' + is_checked;
});