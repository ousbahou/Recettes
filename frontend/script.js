fetch("http://localhost:8080/index.php")
  .then(async (res) => {
      if (!res.ok) {
          throw new Error(`Erreur HTTP! Statut: ${res.status}`);
      }
      return res.json();
  })
  .then(data => {
      const liste = document.getElementById("recettes");
      liste.innerHTML = ""; 
      data.forEach(recette => {
          const li = document.createElement("li");
          li.textContent = recette.titre + " - " + recette.description;
          liste.appendChild(li);
      });
  })
  .catch(error => console.error("Erreur:", error));


document.getElementById("ajoutRecette").addEventListener("submit", async (e) => {
    e.preventDefault();
    
    const titre = document.getElementById("titre").value;
    const description = document.getElementById("description").value;

    const response = await fetch("http://localhost:8080/index.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ titre, description })
    });

    const data = await response.json();
    alert(data.message);
    location.reload();
});

document.getElementById("loginForm").addEventListener("submit", async (e) => {
    e.preventDefault();
    
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;
    
    const response = await fetch("http://localhost:3000/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, password })
    });

    const data = await response.json();
    if (data.token) {
        localStorage.setItem("token", data.token);
        alert("Connexion réussie !");
    } else {
        alert("Échec de connexion !");
    }
});

document.getElementById("registerForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const username = document.getElementById("registerUsername").value;
    const password = document.getElementById("registerPassword").value;

    const response = await fetch("http://localhost:3000/register", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ username, password })
    });

    const data = await response.json();
    if (response.ok) {
        alert("✅ Inscription réussie !");
    } else {
        alert("❌ Erreur: " + data.error);
    }
});

document.getElementById("loginForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    const response = await fetch("http://localhost:3000/login", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ username, password })
    });

    const data = await response.json();
    if (response.ok) {
        alert("✅ Connexion réussie !");
        localStorage.setItem("token", data.token);
    } else {
        alert("❌ Erreur: " + data.error);
    }
});
