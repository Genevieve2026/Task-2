document.addEventListener("DOMContentLoaded", () => {

    fetch("../php/get_sales_data.php")
        .then(res => res.json())
        .then(data => {

            if (!data.length) {
                console.warn("No sales data found");
                return;
            }

            const categories = data.map(item => item.category);
            const totals = data.map(item => item.total);

            const ctx = document.getElementById('salesPieChart');

            if (!ctx) {
                console.error("Canvas not found");
                return;
            }

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: categories,
                    datasets: [{
                        data: totals,
                        backgroundColor: [
                            '#f2a33c',
                            '#308800',
                            '#7b6cca',
                            '#d44f4f',
                            '#dae18d',
                            '#840000',
                            '#c24b4b',
                            '#ff8c42'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        })
        .catch(err => console.error("Fetch error:", err));
});