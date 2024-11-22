let votes = { A: 0, B: 0 };
let totalVotes = 0;

const ctx = document.getElementById('resultsChart').getContext('2d');
const resultsChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Kamala Harris', 'Donald Trump'],
        datasets: [{
            label: 'Votes',
            data: [votes.A, votes.B],
            backgroundColor: ['#3498db', '#e74c3c'],
            borderColor: ['#2980b9', '#c0392b'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

function vote(candidate) {
    if (localStorage.getItem('hasVoted')) {
        alert("You've already voted!");
        return;
    }

    votes[candidate]++;
    totalVotes++;
    localStorage.setItem('hasVoted', 'true');

    updateResults();
    showConfirmation(candidate);
}

function updateResults() {
    resultsChart.data.datasets[0].data = [votes.A, votes.B];
    resultsChart.update();

    document.getElementById('totalVotes').textContent = totalVotes;
}

function showDetailedResults() {
    document.getElementById('votesA').textContent = votes.A;
    document.getElementById('votesB').textContent = votes.B;

    document.getElementById('percentA').textContent = totalVotes > 0 ? ((votes.A / totalVotes) * 100).toFixed(2) + '%' : '0%';
    document.getElementById('percentB').textContent = totalVotes > 0 ? ((votes.B / totalVotes) * 100).toFixed(2) + '%' : '0%';

    document.getElementById('detailedResults').style.display = 'block';
}

function resetVotes() {
    votes = { A: 0, B: 0 };
    totalVotes = 0;
    localStorage.removeItem('hasVoted');

    updateResults();
    document.getElementById('detailedResults').style.display = 'none';
    alert("Votes have been reset!");
}

function showConfirmation(candidate) {
    const confirmationMessage = document.createElement('div');
    confirmationMessage.classList.add('confirmation');
    confirmationMessage.textContent = `Thank you for voting for ${candidate === 'A' ? 'Kamala Harris' : 'Donald Trump'}!`;

    document.body.appendChild(confirmationMessage);

    setTimeout(() => {
        confirmationMessage.classList.add('fade-out');
        setTimeout(() => confirmationMessage.remove(), 500);
    }, 3000);
}
