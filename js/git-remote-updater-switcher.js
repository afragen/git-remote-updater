/**
 * Vanilla Javascript to show and hide the update choices.
 *
 * @access public
 * @package	git-remote-updater
 */

(function () {

	// Hide non-default settings on page load.
	let nonDefault = ['git-remote-updater-site'];

	nonDefault.forEach(function (item) {
		displayNone(item);
	});

	// When the selector changes.
	let selects = document.querySelector('select[ name="git-remote-updater" ]');

	// Only run when on proper tab.
	selects.addEventListener('change', function () {
		let defaults = ['git-remote-updater-site', 'git-remote-updater-repo'];

		// Create difference array.
		let hideMe = remove(defaults, this.value);

		// Hide items with unselected classes.
		hideMe.forEach(function (item) {
			displayNone(item);
		});

		// Show selected setting.
		[this.value].forEach(function (item) {
			display(item);
		});
	});

	// Remove selected element from array and return array.
	function remove(array, element) {
		const index = array.indexOf(element);
		if (index !== -1) {
			array.splice(index, 1);
		}
		return array;
	}

	// Hide element.
	function displayNone(item) {
		x = document.getElementsByClassName(item)[0];
		x.style.display = 'none';
	}

	// Display element.
	function display(item) {
		x = document.getElementsByClassName(item)[0];
		x.style.display = '';
	}

})();
