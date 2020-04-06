#!/usr/bin/env bash
BIN_PATH="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
DISTPATH="${BIN_PATH}/../docs/dist"
WIKIPATH="${BIN_PATH}/../docs/wiki"

if [ ! -d "${BIN_PATH}/../vendor/phpdocumentor" ]; then
    rm composer.json composer.lock
    composer require phpdocumentor/phpdocumentor
    composer require evet/phpdoc-md
fi

rm -rf "${BIN_PATH}/../docs"

mkdir -p "${DISTPATH}"

./vendor/bin/phpdoc
./vendor/bin/phpdocmd "${DISTPATH}/structure.xml" "${DISTPATH}"
rm "${DISTPATH}/structure.xml"

if [ -d "${WIKIPATH}" ]; then
  rm -rf "${WIKIPATH}"
fi
git clone git@nxfifteen.me.uk:nx-health/store.wiki.git "${WIKIPATH}"

if [ -d "${WIKIPATH}/phpdoc" ]; then
  rm -rf "${WIKIPATH}/phpdoc"
fi
mkdir -p "${WIKIPATH}/phpdoc"
mv "${DISTPATH}/ApiIndex.md" "${WIKIPATH}/phpdoc.md"

# shellcheck disable=SC2164
cd "${DISTPATH}"
# shellcheck disable=SC2044
# shellcheck disable=SC2006
for DISTFILE in `find ./ -type f -name '*.md'`
do
    if [ "${DISTFILE}" != "./ApiIndex.md" ]; then
        DESTFILE=${DISTFILE//-//}
        echo "$DISTFILE => $DESTFILE"

        DESTDIR=$(dirname "${DESTFILE}")
        if [ ! -d "${WIKIPATH}/phpdoc/${DESTDIR}" ]; then mkdir -p "${WIKIPATH}/phpdoc/${DESTDIR}"; fi

        mv "$DISTFILE" "${WIKIPATH}/phpdoc/${DESTFILE}"

        MDLINKORI=${DISTFILE/.\//}
        MDLINKNEW=${DESTFILE/.\//}
        MDLINKNEW=${MDLINKNEW/.md/}
        sed -i "s|${MDLINKORI}|${MDLINKNEW}|g" "${WIKIPATH}/phpdoc.md"
    fi
done

sed -i "s|](|](phpdoc/|g" "${WIKIPATH}/phpdoc.md"

# shellcheck disable=SC2164
cd "${WIKIPATH}/"
git add .
git commit -m "Updated php documentor file"
git push
