#ifndef ZEND_WEBCAM_TRACE_H
#define ZEND_WEBCAM_TRACE_H

#include "../Zend/zend_compile.h"
#include "zend.h"

extern void vld_external_trace(zend_execute_data *execute_data, const zend_op *opline);
extern void witcher_cgi_trace_finish(void);
extern void witcher_cgi_trace_init(char * ch_shm_id);
extern void vld_external_trace(zend_execute_data *execute_data, const zend_op *opline);
extern void vld_start_trace();

#define VM_TRACE_START() vld_start_trace();
#define VM_TRACE(op) vld_external_trace(execute_data, opline);
#define VM_TRACE_END() witcher_cgi_trace_finish();


#endif /* ZEND_WEBCAM_TRACE_H */