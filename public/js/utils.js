const arePasswordsSame = (password, confirmedPassword) => {
    return password === confirmedPassword;
}

const isEmail = (email) => {
    return /\S+@\S+\.\S+/.test(email);
}

const isTwoOrMoreWords = (string) => {
    const words = string.trim().split(/\s+/);
    return words.length >= 2;
}

const isPasswordStrong = (password) => {
    const minLength = 8;
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumbers = /\d/.test(password);
    const hasSpecialChars = /[!@#$%^&*(),.?":{}|<>]/.test(password);

    return password.length >= minLength && hasUpperCase && hasLowerCase && hasNumbers && hasSpecialChars;
}

const redirectToTargetOrDefault = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const redirectUrl =  urlParams.get('redirect_url');
    if (redirectUrl) {
        window.location.href = redirectUrl;
    } else {
        window.location.href = '/';
    }
}

export { arePasswordsSame, isEmail, isTwoOrMoreWords, isPasswordStrong, redirectToTargetOrDefault };
