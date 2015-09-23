PHP_ARG_WITH(fann, for fann support,
Make sure that the comment is aligned:
[  --with-fann             Include fann support])

if test "$PHP_FANN" != "no"; then
  SEARCH_PATH="/usr/local /usr"
  SEARCH_FOR="/include/fann.h"

  if test -r $PHP_FANN/$SEARCH_FOR; then
    FANN_DIR=$PHP_FANN
  else
    AC_MSG_CHECKING([for fann files in default path])
    for i in $SEARCH_PATH ; do
      if test -r $i/$SEARCH_FOR; then
        FANN_DIR=$i
        AC_MSG_RESULT(found in $i)
      fi
   done
  fi
  if test -z "$FANN_DIR"; then
    AC_MSG_RESULT([not found])
    AC_MSG_ERROR([Please reinstall the fann distribution])
  fi

  PHP_ADD_INCLUDE($FANN_DIR/include)

  LIBNAME=fann
  LIBSYMBOL=fann_get_MSE

  PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  [
    PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $FANN_DIR/lib, FANN_SHARED_LIBADD)
    AC_DEFINE(HAVE_FANNLIB,1,[ ])
  ],[
    AC_MSG_ERROR([wrong fann lib version or lib not found])
  ],[
    -L$FANN_DIR/lib -lm -ldl
  ])
  PHP_SUBST(FANN_SHARED_LIBADD)

  PHP_NEW_EXTENSION(fann, fann.c, $ext_shared)
fi
