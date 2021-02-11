/**
 * The file contains functions returning the desired validation message.
 */
const REQUIRED = () => `Задължително!`;
const MIN = (length) => `Полето трябва да бъде минимум ${length} символа!`;
const MAX = (length) => `Полето не трябва да бъде повече от ${length} символа!`;
const INVALID_EMAIL = () => `Невалиден имейл адрес!`;
const FORBIDDEN_CHARACTERS = () => `Полето съдържа специални символи!`;

const myValidate = (value) => ({
  MIN: (length) => {
    if (value.length < length) {
      return `Полето трябва да бъде минимум ${length} символа!`;
    }
  },
  MAX: (length) => {
    if (value.length > length) {
      `Полето не трябва да бъде повече от ${length} символа!`;
    }
  },
  REQUIRED: () => {
    if (!value) {
      return `Задължително!`;
    }
  },
  SPECIAL_CHARACTERS: () => {
    if (!/^[a-z \u0400-\u04FF ,.'-]+$/i.test(value)) {
      return `Полето съдържа специални символи!`;
    }
  },
  EMAIL: () => {
    if (!/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i.test(value)) {
      return `Невалиден имейл адрес!`;
    }
  },
});

export { REQUIRED, MIN, MAX, INVALID_EMAIL, FORBIDDEN_CHARACTERS, myValidate };
