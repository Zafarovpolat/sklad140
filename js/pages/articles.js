const articlesData = [
	{
		id: 0,
		title: '5 быстрых советов, которые помогут ускорить работу линий раздачи',
		date: '12.06.2024',
		description:
			'Случался ли когда-нибудь подобный сценарий с одним из ваших кассиров? Звонит звонок, и прежде чем они успевают войти в свои POS-терминалы, вокруг всей столовой выстраивается очередь детей, ожидающих горячего',
		text: '-',
		imgTag: '<img src="./images/content/news/1.webp" srcset="./images/content/news/1.webp 1x, ./images/content/news/1@2x.webp 2x" alt="">',
	},
	{
		id: 1,
		title: 'Какой тостер выбрать для заведения?',
		date: '12.06.2024',
		description:
			'Одно дело поджарить немного хрустящего хлебца, другое дело поставить жареный хлеб во главу стола. Какой тостер выбрать для заведения? Взять конвейерный тостер или аппарат для жарки булочек на гриле? Хватит',
		text: '-',
		imgTag: '<img src="./images/content/news/2.webp" srcset="./images/content/news/2.webp 1x, ./images/content/news/2@2x.webp 2x" alt="">',
	},
	{
		id: 2,
		title: 'Выброшенная еда',
		date: '12.06.2024',
		description:
			'Случался ли когда-нибудь подобный сценарий с одним из ваших кассиров? Звонит звонок, и прежде чем они успевают войти в свои POS-терминалы, вокруг всей столовой выстраивается очередь детей, ожидающих горячего',
		text: '-',
		imgTag: '<img src="./images/content/news/3.webp" srcset="./images/content/news/3.webp 1x, ./images/content/news/3@2x.webp 2x" alt="">',
	},
	{
		id: 3,
		title: 'Что такое стретч-пленка',
		date: '12.06.2024',
		description:
			'Случался ли когда-нибудь подобный сценарий с одним из ваших кассиров? Звонит звонок, и прежде чем они успевают войти в свои POS-терминалы, вокруг всей столовой выстраивается очередь детей, ожидающих горячего',
		text: '-',
		imgTag: '<img src="./images/content/news/4.webp" srcset="./images/content/news/4.webp 1x, ./images/content/news/4@2x.webp 2x" alt="">',
	},
	{
		id: 4,
		title: '5 советов, как готовить на углях как профессионал',
		date: '12.06.2024',
		description:
			'Случался ли когда-нибудь подобный сценарий с одним из ваших кассиров? Звонит звонок, и прежде чем они успевают войти в свои POS-терминалы, вокруг всей столовой выстраивается очередь детей, ожидающих горячего',
		text: '-',
		imgTag: '<img src="./images/content/news/5.webp" srcset="./images/content/news/5.webp 1x, ./images/content/news/5@2x.webp 2x" alt="">',
	},
]

document.addEventListener('DOMContentLoaded', function () {
	const contentContainer = document.querySelector('.articles__inner')
	const sentinel = document.getElementById('sentinel')

	// Начальное количество элементов
	let itemCount = 0
	// Количество элементов для загрузки за раз
	const itemsPerLoad = 4
	// Максимальное количество элементов (для демонстрации)
	const maxItems = 25

	// Функция для создания скелетонов
	const createSkeletonBlocks = count => {
		for (let i = 0; i < count; i++) {
			const skeletonBlock = document.createElement('div')
			skeletonBlock.className = 'skeleton-block'

			const skeletonTitle = document.createElement('div')
			skeletonTitle.className = 'skeleton-title'

			const skeletonText1 = document.createElement('div')
			skeletonText1.className = 'skeleton-text short'

			const skeletonText2 = document.createElement('div')
			skeletonText2.className = 'skeleton-text medium'

			const skeletonText3 = document.createElement('div')
			skeletonText3.className = 'skeleton-text long'

			skeletonBlock.appendChild(skeletonTitle)
			skeletonBlock.appendChild(skeletonText1)
			skeletonBlock.appendChild(skeletonText2)
			skeletonBlock.appendChild(skeletonText3)

			contentContainer.appendChild(skeletonBlock)
		}
	}

	// Функция для создания контентных блоков
	const createContentBlocks = count => {
		// Удаляем скелетоны
		const skeletonBlocks = document.querySelectorAll('.skeleton-block')
		skeletonBlocks.forEach(block => block.remove())

		// Создаем новые блоки контента
		for (let i = 0; i < count; i++) {
			itemCount++
			if (itemCount >= maxItems) return

			contentContainer.insertAdjacentHTML(
				'beforeend',
				`
					<article class="news-card">
                        <div class="news-card__info">
                            <p class="news-card__date">${articlesData[i].date}</p>
                            <p class="news-card__title">${articlesData[i].title}</p>
                            <p class="news-card__description">${articlesData[i].description}</p>
                        </div>
                        <div class="news-card__img">
                            ${articlesData[i].imgTag}
                        </div>
                    </article>
				`
			)
		}
	}

	// Функция для имитации загрузки данных
	const loadMoreItems = () => {
		if (itemCount >= maxItems) return

		// Создаем скелетоны
		createSkeletonBlocks(itemsPerLoad)

		// Имитируем задержку загрузки данных
		setTimeout(() => {
			// Создаем контентные блоки
			createContentBlocks(itemsPerLoad)
		}, 1000)
	}

	// Настройка Intersection Observer
	const observer = new IntersectionObserver(
		entries => {
			entries.forEach(entry => {
				if (entry.isIntersecting && itemCount < maxItems) {
					loadMoreItems()
				}
			})
		},
		{
			rootMargin: '100px', // Загружаем заранее, когда элемент находится в 100px от видимой области
		}
	)

	// Начинаем наблюдение за sentinel элементом
	observer.observe(sentinel)

	// Загружаем начальные элементы
	loadMoreItems()
})
