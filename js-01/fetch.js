const fetchUserData = (url) => {
    return fetch(url)
        .then(res => {
            if (!res.ok) { // response != 2xx
                throw new Error(`HTTP error! Status: ${res.status}`);
            }
            return res.json();
        })
        .catch(error => {
            console.error("Fetch error:", error.message);
            throw error;
        });
};

fetchUserData('URL')
    .then(users => console.log(users))
    .catch(err => console.log("Something went wrong:", err.message));
