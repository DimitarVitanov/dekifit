// A training plan can be split into weeks: each day then carries a `week` number.
// A client starts the plan on the day they joined or the plan was created,
// whichever is later, and Day 1 of week 1 falls on that date.

export function planWeeks(plan) {
    return [...new Set(plan.days.map(day => day.week).filter(Boolean))].sort((a, b) => a - b);
}

// Days of a weekly plan by week: { 1: [{ day, index }, …], … }, index being the position in plan.days.
export function daysByWeek(plan) {
    return plan.days.reduce((weeks, day, index) => {
        if (day.week) (weeks[day.week] ??= []).push({ day, index });
        return weeks;
    }, {});
}

// Local midnight of the plan's first day.
export function planStart(plan, memberSince) {
    const start = new Date(Math.max(new Date(plan.created_at).getTime() || 0, new Date(memberSince).getTime() || 0));
    start.setHours(0, 0, 0, 0);
    return start;
}

// The calendar date `offset` days into the plan.
export function planDate(plan, memberSince, offset) {
    const start = planStart(plan, memberSince);
    return new Date(start.getFullYear(), start.getMonth(), start.getDate() + offset);
}

export function daysIntoPlan(plan, memberSince) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return Math.max(0, Math.round((today - planStart(plan, memberSince)) / 86400000));
}

// The week the client should be on now (the last week once they're past the end), or null for a plan without weeks.
export function currentWeek(plan, memberSince) {
    const weeks = planWeeks(plan);
    if (!weeks.length) return null;
    const week = Math.floor(daysIntoPlan(plan, memberSince) / 7) + 1;
    const last = weeks[weeks.length - 1];
    return weeks.includes(week) ? week : week > last ? last : weeks[0];
}

// Position in plan.days of today's day, or the first day of the current week.
export function todayIndex(plan, memberSince) {
    const week = currentWeek(plan, memberSince);
    if (!week) return 0;
    const days = daysByWeek(plan)[week];
    return (days[daysIntoPlan(plan, memberSince) - (week - 1) * 7] ?? days[0]).index;
}

// The calendar date of plan.days[index] in a weekly plan.
export function dayDate(plan, memberSince, index) {
    const week = plan.days[index]?.week;
    if (!week) return null;
    return planDate(plan, memberSince, (week - 1) * 7 + daysByWeek(plan)[week].findIndex(entry => entry.index === index));
}

// "Day 3 — Rest · 10–15k steps" → "Rest", for tight spaces like calendar cells.
export function dayLabel(name) {
    return name.replace(/^day\s*\d+\s*[—–:-]?\s*/i, '').split(/\s[·(]/)[0].trim() || name;
}
