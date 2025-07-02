export default function FloorMapCheckTwo({ mainMapContainerRef, type, bgColor }) {
    
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
        <div id="mainMapContainer" ref={mainMapContainerRef} style={{  height: vh, backgroundColor: bgColor }} />
    </>
}