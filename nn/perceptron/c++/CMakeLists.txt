project(Perc)
cmake_minimum_required(VERSION 3.2)
set(CMAKE_CXX_FLAGS "${CMAKE_CXX_FLAGS} -std=c++11")
set(CMAKE_BUILD_TYPE Debug)
set(EXECUTABLE_NAME "Perc")
file(GLOB SRC
    "*.h"
    "*.cpp"
)

#Subdirectories
option(USE_CLANG "build application with clang" ON)

find_package(Armadillo REQUIRED)
include_directories(${ARMADILLO_INCLUDE_DIRS})

set(CMAKE_RUNTIME_OUTPUT_DIRECTORY "${CMAKE_CURRENT_SOURCE_DIR}/bin")
add_executable(${EXECUTABLE_NAME} ${SRC} )
TARGET_LINK_LIBRARIES(  ${EXECUTABLE_NAME}  ${ARMADILLO_LIBRARIES} )