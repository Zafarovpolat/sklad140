// Tabs
const tabs = document.querySelectorAll('.clients-tab')
const contents = document.querySelectorAll('.clients-content')

tabs.forEach(tab =>
	tab.addEventListener('click', () => {
		const currentDataTab = tab.getAttribute('data-tab')

		tabs.forEach(tab => tab.classList.remove('clients-tab--active'))
		tab.classList.add('clients-tab--active')

		contents.forEach(content => {
			console.log(content)
			content.classList.remove('clients-content--active')

			if (content.getAttribute('data-content') === currentDataTab) {
				content.classList.add('clients-content--active')
			}
		})
	})
)

// FAQ
document.addEventListener('click', e => {
	const clientsQuestionsItem = e.target.closest('.clients-questions-item')

	if (clientsQuestionsItem) {
		clientsQuestionsItem.classList.toggle('clients-questions-item--active')
	}
})
