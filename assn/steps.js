/* Step checks for DJ4E assignments.
   Each box is stored under a key built from where it sits in the page
   (an XPath-style path from #step-root) plus the step text, so the
   markdown does not carry ids. Checks stay in localStorage. */
(function () {
  if (window.__dj4eSteps) return;
  window.__dj4eSteps = true;

  var root = document.getElementById('step-root');
  if (!root) return;

  var boxes = root.querySelectorAll('input.step-box');
  if (!boxes.length) return;

  var pageKey = 'dj4e-steps:' + location.pathname;

  function stepHost(box) {
    return box.closest('.step') || box.parentElement;
  }

  function stepPath(box) {
    var node = stepHost(box);
    var parts = [];
    while (node && node !== root) {
      var index = 1;
      var sib = node;
      while ((sib = sib.previousElementSibling)) {
        if (sib.tagName === node.tagName) index++;
      }
      parts.unshift(node.tagName.toLowerCase() + '[' + index + ']');
      node = node.parentElement;
    }
    return parts.join('/');
  }

  function stepText(box) {
    var host = stepHost(box);
    var clone = host.cloneNode(true);
    var nested = clone.querySelectorAll('input.step-box');
    for (var i = 0; i < nested.length; i++) nested[i].remove();
    return (clone.textContent || '').replace(/\s+/g, ' ').trim().slice(0, 180);
  }

  function load() {
    try {
      var raw = localStorage.getItem(pageKey);
      if (!raw) return { v: 1, steps: {} };
      var data = JSON.parse(raw);
      if (!data || typeof data !== 'object' || !data.steps) return { v: 1, steps: {} };
      return data;
    } catch (e) {
      return { v: 1, steps: {} };
    }
  }

  function save(data) {
    try {
      localStorage.setItem(pageKey, JSON.stringify(data));
    } catch (e) {}
  }

  function paint(box) {
    var host = stepHost(box);
    if (host) host.classList.toggle('step-done', box.checked);
  }

  var data = load();
  var stored = data.steps;
  var storedByText = {};
  Object.keys(stored).forEach(function (path) {
    var rec = stored[path];
    if (!rec || !rec.text) return;
    (storedByText[rec.text] = storedByText[rec.text] || []).push(path);
  });

  var infos = [];
  var pageByText = {};
  for (var i = 0; i < boxes.length; i++) {
    var box = boxes[i];
    var text = stepText(box);
    var path = stepPath(box);
    var info = { box: box, path: path, text: text };
    infos.push(info);
    (pageByText[text] = pageByText[text] || []).push(info);
    box.setAttribute('aria-label', text ? ('Mark done: ' + text.slice(0, 120)) : 'Mark this step done');
  }

  var migrated = false;
  infos.forEach(function (info) {
    var rec = stored[info.path];
    if (rec && rec.text === info.text) {
      info.box.checked = !!rec.done;
      return;
    }
    var paths = storedByText[info.text] || [];
    var onPage = pageByText[info.text] || [];
    if (paths.length === 1 && onPage.length === 1 && stored[paths[0]]) {
      var old = stored[paths[0]];
      info.box.checked = !!old.done;
      delete stored[paths[0]];
      stored[info.path] = { done: !!old.done, text: info.text };
      migrated = true;
    }
  });
  if (migrated) save(data);

  infos.forEach(function (info) {
    paint(info.box);
    info.box.addEventListener('change', function () {
      var fresh = load();
      fresh.steps[info.path] = { done: info.box.checked, text: info.text };
      save(fresh);
      data = fresh;
      stored = fresh.steps;
      paint(info.box);
      markCurrent();
      updateProgress();
    });
  });

  var bar = document.createElement('div');
  bar.id = 'step-progress';
  bar.setAttribute('role', 'region');
  bar.setAttribute('aria-label', 'Step progress');
  bar.innerHTML =
    '<progress id="step-progress-bar" value="0" max="1"></progress>' +
    '<span id="step-progress-label" aria-live="polite"></span>' +
    '<span class="step-progress-note">Saved in this browser</span>' +
    '<button type="button" id="step-next">Next open step</button>' +
    '<button type="button" id="step-clear">Clear checks</button>';
  root.parentNode.insertBefore(bar, root);

  var label = document.getElementById('step-progress-label');
  var meter = document.getElementById('step-progress-bar');
  var nextBtn = document.getElementById('step-next');
  var clearBtn = document.getElementById('step-clear');

  function firstOpen() {
    for (var n = 0; n < boxes.length; n++) {
      if (!boxes[n].checked) return boxes[n];
    }
    return null;
  }

  function markCurrent() {
    var hosts = root.querySelectorAll('.step-current');
    for (var n = 0; n < hosts.length; n++) hosts[n].classList.remove('step-current');
    var done = 0;
    for (var i = 0; i < boxes.length; i++) if (boxes[i].checked) done++;
    if (done === 0 || done === boxes.length) return;
    var open = firstOpen();
    var host = open && stepHost(open);
    if (host) host.classList.add('step-current');
  }

  function updateProgress() {
    var done = 0;
    for (var i = 0; i < boxes.length; i++) if (boxes[i].checked) done++;
    var total = boxes.length;
    meter.max = total;
    meter.value = done;
    if (done === total) {
      label.textContent = 'All ' + total + (total === 1 ? ' step marked' : ' steps marked');
    } else {
      label.textContent = done + ' of ' + total + (total === 1 ? ' step marked' : ' steps marked');
    }
    nextBtn.disabled = done === total;
    clearBtn.disabled = done === 0;
  }

  nextBtn.addEventListener('click', function () {
    var open = firstOpen();
    if (!open) return;
    var host = stepHost(open);
    if (host && host.scrollIntoView) host.scrollIntoView({ behavior: 'smooth', block: 'center' });
    open.focus();
  });

  clearBtn.addEventListener('click', function () {
    if (!window.confirm('Clear all step checks on this page? This only affects this browser.')) return;
    for (var i = 0; i < boxes.length; i++) {
      boxes[i].checked = false;
      paint(boxes[i]);
    }
    save({ v: 1, steps: {} });
    data = { v: 1, steps: {} };
    stored = data.steps;
    markCurrent();
    updateProgress();
  });

  markCurrent();
  updateProgress();
})();
