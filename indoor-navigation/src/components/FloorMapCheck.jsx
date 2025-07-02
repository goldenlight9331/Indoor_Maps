export default function FloorMapCheck({ secondMapContainerRef, type, bgColor }) {

    const calculateVh = () => {
        if(type.startsWith('landscape')){
            return '90vh';
        }
        else
        {
            return '55vh';
        }
    }
    let vh = calculateVh();

    return <>
        <div id="secondMapContainer" ref={secondMapContainerRef} style={{ height: vh, backgroundColor: bgColor }} />

    </>
}