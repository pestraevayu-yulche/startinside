// Без JSX - используем React.createElement
function DirectionCard(props) {
    const { direction } = props;
    
    return React.createElement(
        'div', 
        { className: 'col-lg-4 col-md-6 mb-4' },
        React.createElement(
            'div',
            { className: 'direction-card' },
            [
                React.createElement('h3', { className: 'direction-title' }, direction.name),
                React.createElement('p', { className: 'direction-description' }, direction.description),
                React.createElement(
                    'div',
                    { className: 'direction-skills' },
                    [
                        React.createElement('h5', null, 'Ключевые навыки:'),
                        React.createElement('p', null, direction.skills)
                    ]
                ),
                React.createElement(
                    'div',
                    { className: 'direction-career' },
                    [
                        React.createElement('h5', null, 'Карьерный путь:'),
                        React.createElement('p', null, direction.career_paths)
                    ]
                ),
                window.isLoggedIn === true 
                    ? React.createElement(
                        'a', 
                        { 
                            href: `direction_test.php?direction=${direction.id}`,
                            className: 'btn btn-primary test-btn'
                        },
                        'Пройти тестирование'
                    )
                    : React.createElement(
                        'button',
                        {
                            className: 'btn btn-secondary test-btn',
                            'data-bs-toggle': 'modal',
                            'data-bs-target': '#authRequiredModal'
                        },
                        'Войдите для тестирования'
                    )
            ]
        )
    );
}

class DirectionsApp extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            directions: [],
            loading: true,
            error: null
        };
    }

    componentDidMount() {
        this.fetchDirections();
    }

    fetchDirections = () => {
        this.setState({ loading: true });
        
        fetch("api.php")
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                this.setState({ 
                    directions: data, 
                    loading: false,
                    error: null 
                });
            })
            .catch(error => {
                console.error('Error fetching directions:', error);
                this.setState({ 
                    loading: false, 
                    error: error.message 
                });
            });
    }

    render() {
        const { directions, loading, error } = this.state;

        if (loading) {
            return React.createElement(
                'div',
                { className: 'row' },
                React.createElement(
                    'div',
                    { className: 'col-12 text-center' },
                    React.createElement(
                        'div',
                        { className: 'loading-spinner' },
                        [
                            React.createElement(
                                'div',
                                { 
                                    className: 'spinner-border text-light',
                                    role: 'status'
                                },
                                React.createElement('span', { className: 'visually-hidden' }, 'Загрузка...')
                            ),
                            React.createElement('p', { className: 'mt-2' }, 'Загрузка направлений...')
                        ]
                    )
                )
            );
        }

        if (error) {
            return React.createElement(
                'div',
                { className: 'row' },
                React.createElement(
                    'div',
                    { className: 'col-12' },
                    React.createElement(
                        'div',
                        { className: 'alert alert-danger' },
                        [
                            React.createElement('h5', null, 'Ошибка при загрузке направлений'),
                            React.createElement('p', null, error),
                            React.createElement(
                                'button',
                                {
                                    className: 'btn btn-warning',
                                    onClick: this.fetchDirections
                                },
                                'Попробовать снова'
                            )
                        ]
                    )
                )
            );
        }

        if (directions.length === 0) {
            return React.createElement(
                'div',
                { className: 'row' },
                React.createElement(
                    'div',
                    { className: 'col-12' },
                    React.createElement(
                        'div',
                        { className: 'alert alert-info' },
                        'Направления временно недоступны'
                    )
                )
            );
        }

        return React.createElement(
            'div',
            { className: 'row' },
            directions.map(direction => 
                React.createElement(DirectionCard, {
                    key: direction.id,
                    direction: direction
                })
            )
        );
    }
}

// Рендеринг
const container = document.getElementById('react-directions');
if (container) {
    const root = ReactDOM.createRoot(container);
    root.render(React.createElement(DirectionsApp));
}
