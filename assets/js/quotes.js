// Array of educational quotes
const quotes = [
  {
    text: "Education is not the filling of a pail, but the lighting of a fire.",
    author: "William Butler Yeats",
  },
  {
    text: "The beautiful thing about learning is that nobody can take it away from you.",
    author: "B.B. King",
  },
  {
    text: "Education is the most powerful weapon which you can use to change the world.",
    author: "Nelson Mandela",
  },
  {
    text: "The roots of education are bitter, but the fruit is sweet.",
    author: "Aristotle",
  },
  {
    text: "Education is not preparation for life; education is life itself.",
    author: "John Dewey",
  },
  {
    text: "Learning is a treasure that will follow its owner everywhere.",
    author: "Chinese Proverb",
  },
  {
    text: "Education is the key to unlock the golden door of freedom.",
    author: "George Washington Carver",
  },
  {
    text: "The more that you read, the more things you will know. The more that you learn, the more places you'll go.",
    author: "Dr. Seuss",
  },
  {
    text: "Education is not just about going to school and getting a degree. It's about widening your knowledge and absorbing the truth about life.",
    author: "Shakuntala Devi",
  },
  {
    text: "Live as if you were to die tomorrow. Learn as if you were to live forever.",
    author: "Mahatma Gandhi",
  },
];

// Function to get random quotes
function getRandomQuotes() {
  // Create a copy of the quotes array
  const quotesCopy = [...quotes];
  // Shuffle the array
  for (let i = quotesCopy.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [quotesCopy[i], quotesCopy[j]] = [quotesCopy[j], quotesCopy[i]];
  }
  // Return first two quotes
  return [quotesCopy[0], quotesCopy[1]];
}

// Function to update quotes
function updateQuotes() {
  const [quote1, quote2] = getRandomQuotes();

  document.getElementById("quote1-text").textContent = `"${quote1.text}"`;
  document.getElementById("quote1-author").textContent = `- ${quote1.author}`;
  document.getElementById("quote2-text").textContent = `"${quote2.text}"`;
  document.getElementById("quote2-author").textContent = `- ${quote2.author}`;
}

// Update quotes when the page loads
document.addEventListener("DOMContentLoaded", updateQuotes);
