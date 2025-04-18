function removeEmail(user) {
    const { email, ...rest } = user;
    return rest;
}

const user = {
    name: "John Doe",
    email: "john@example.com",
    address: {
        city: "New York",
        state: "NY"
    }
};

const newUser = removeEmail(user);
console.log(newUser);
