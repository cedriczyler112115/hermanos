import test from 'node:test';
import assert from 'node:assert/strict';
import { homeSlideshowFindNextIndexBounded } from '../../resources/js/app.js';

test('unit: homeSlideshowFindNextIndexBounded moves forward without wrapping', () => {
    const broken = new Set();
    assert.equal(homeSlideshowFindNextIndexBounded(1, 1, broken, 5), 1);
    assert.equal(homeSlideshowFindNextIndexBounded(5, 1, broken, 5), -1);
});

test('unit: homeSlideshowFindNextIndexBounded moves backward without wrapping', () => {
    const broken = new Set();
    assert.equal(homeSlideshowFindNextIndexBounded(3, -1, broken, 5), 3);
    assert.equal(homeSlideshowFindNextIndexBounded(-1, -1, broken, 5), -1);
});

test('unit: homeSlideshowFindNextIndexBounded skips broken indices', () => {
    const broken = new Set([1, 2, 3]);
    assert.equal(homeSlideshowFindNextIndexBounded(1, 1, broken, 5), 4);
    assert.equal(homeSlideshowFindNextIndexBounded(3, -1, broken, 5), 0);
});

test('integration: sequential navigation stops at boundaries (no loop)', () => {
    const broken = new Set();
    const count = 4;
    let index = 0;

    const next = () => homeSlideshowFindNextIndexBounded(index + 1, 1, broken, count);
    const prev = () => homeSlideshowFindNextIndexBounded(index - 1, -1, broken, count);

    assert.equal(prev(), -1);
    assert.equal(next(), 1);

    index = 1;
    assert.equal(prev(), 0);
    assert.equal(next(), 2);

    index = 2;
    assert.equal(next(), 3);

    index = 3;
    assert.equal(next(), -1);
    assert.equal(prev(), 2);
});

test('integration: navigation respects boundaries with broken slides', () => {
    const broken = new Set([1, 2]);
    const count = 5;
    let index = 0;

    const next = () => homeSlideshowFindNextIndexBounded(index + 1, 1, broken, count);
    const prev = () => homeSlideshowFindNextIndexBounded(index - 1, -1, broken, count);

    assert.equal(prev(), -1);
    assert.equal(next(), 3);

    index = 4;
    assert.equal(next(), -1);
    assert.equal(prev(), 3);
});
