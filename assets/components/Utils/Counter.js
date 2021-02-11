import { useState, useEffect } from "react";
import Proptypes from "prop-types";

function easeInOutQuad(t, b, c, d) {
  if ((t /= d / 2) < 1) return (c / 2) * t * t + b;
  return (-c / 2) * (--t * (t - 2) - 1) + b;
}

function easeOutQuad(t, b, c, d) {
  return -c * (t /= d) * (t - 2) + b;
}

function easeInQuad(t, b, c, d) {
  return c * (t /= d) * t + b;
}

function Counter(props) {
  const { duration, end, start, effect } = props;
  const [current, setCurrent] = useState(start);

  let period = (duration * 1000) / (end - start);

  useEffect(() => {
    let time;

    switch (effect) {
      case "in":
        time = easeInQuad(current, 0, period, end - start);
      case "out":
        time = easeOutQuad(current, 0, period, end - start);
      case "in-out":
        time = easeInOutQuad(current, 0, period, end - start);
    }

    const timeout = setTimeout(() => {
      console.log("start", time, current);
      let number = current;
      if (number < end) {
        setCurrent(++number);
      }
    }, time);

    return () => clearTimeout(timeout);
  }, [current]);

  return current.toString();
}

Counter.defaultProps = {
  start: 0,
  duration: 1,
  effect: "in",
};

Counter.propTypes = {
  duration: Proptypes.number,
  end: Proptypes.number.isRequired,
  start: Proptypes.number,
  effect: Proptypes.oneOf(["in", "in-out", "out"]),
};

export default Counter;
